<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Diet;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AiDietController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * API 1: فحص اكتمال الملف الشخصي
     * GET /api/user/check-completion
     */
    public function checkProfileCompletion(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $client = Client::where('clients_id', $user->user_id)
            ->with(['bodyData', 'lifestyle'])
            ->first();

        // إذا لم يكن هناك سجل عميل أصلاً
        if (! $client) {
            return response()->json([
                'status' => 'success',
                'is_complete' => false,
                'missing' => ['client_record', 'body_data', 'lifestyle_data'],
            ]);
        }

        $missing = [];

        // 1. فحص البيانات الجسدية
        if (! $client->bodyData) {
            $missing[] = 'body_data';
        } else {
            // تحقق من الحقول الأساسية
            if (! $client->bodyData->height_cm) {
                $missing[] = 'height';
            }
            if (! $client->bodyData->weight_kg) {
                $missing[] = 'weight';
            }
            if (! $client->bodyData->birth_date) {
                $missing[] = 'birth_date';
            }
            if (! $client->bodyData->sex) {
                $missing[] = 'gender';
            }
        }

        // 2. فحص نمط الحياة
        if (! $client->lifestyle) {
            $missing[] = 'lifestyle_data';
        } else {
            if (! $client->lifestyle->activity_level) {
                $missing[] = 'activity_level';
            }
        }

        // 3. (اختياري) فحص البيانات الطبية
        // عادة البيانات الطبية قد تكون فارغة (لا توجد أمراض)، لذا لا نعتبرها "ناقصة" إلا إذا كان المنطق يتطلب تأكيداً "لا يوجد"

        $isComplete = empty($missing);

        return response()->json([
            'status' => 'success',
            'is_complete' => $isComplete,
            'missing' => $missing,
            'message' => $isComplete ? 'البيانات مكتملة' : 'يرجى إكمال البيانات الناقصة',
        ]);
    }

    /**
     * API 2: اقتراح حمية عامة باستخدام الذكاء الاصطناعي
     * POST /api/ai/suggest-diet
     */
    public function suggestDiet(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $client = Client::where('clients_id', $user->user_id)
            ->with(['bodyData', 'lifestyle', 'chronicDiseases', 'allergies'])
            ->first();

        if (! $client || ! $client->bodyData) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات المستخدم غير مكتملة',
            ], 400);
        }

        // 1. تحضير بيانات المستخدم للخدمة
        $userData = $this->prepareUserData($client);

        // 2. جلب الحميات العامة
        $publicDiets = Diet::where('is_public', true)->get();

        if ($publicDiets->isEmpty()) {
            return response()->json([
                'status' => 'no_match', // حالة خاصة للفرونت
                'message' => 'لا توجد حميات عامة متاحة حالياً.',
            ]);
        }

        try {
            // 3. استدعاء Gemini لاختيار حمية
            $suggestedDietId = $this->geminiService->suggestDietForUser($userData, $publicDiets);

            if ($suggestedDietId && $suggestedDietId != 0) {
                $diet = Diet::find($suggestedDietId);

                // حفظ الحمية للمستخدم فوراً حسب طلب النظام
                // 1. ربط الحمية بالمستخدم (للسجل)
                // تحقق إن لم تكن موجودة مسبقاً في الـ pivot
                if (! $client->diets()->where('diets.diets_id', $suggestedDietId)->exists()) {
                    $client->diets()->attach($suggestedDietId);
                }

                // 2. تعيينها كالحمية المتبعة حالياً
                $client->diets_id = $suggestedDietId;
                $client->save();

                return response()->json([
                    'status' => 'success',
                    'action' => 'found_public',
                    'data' => $diet,
                    'message' => 'تم العثور على حمية مناسبة وتم تعيينها لك بنجاح.',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'عذراً، لا توجد حميات متاحة حالياً في النظام.',
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Diet Suggestion Error: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء الاتصال بالذكاء الاصطناعي',
                'debug' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 3: توليد حمية خاصة (بعد موافقة المستخدم)
     * POST /api/ai/generate-private-diet
     */
    public function generatePrivateDiet(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $client = Client::where('clients_id', $user->user_id)
            ->with(['bodyData', 'lifestyle', 'chronicDiseases', 'allergies'])
            ->first();

        if (! $client) {
            return response()->json(['status' => 'error', 'message' => 'Client not found'], 404);
        }

        $userData = $this->prepareUserData($client);

        // جلب عينة من الوجبات (يمكن تحسينها لجلب وجبات مطابقة قليلاً للفلتر أولياً)
        $availableMeals = \App\Models\Meal::take(50)->get();

        try {
            DB::beginTransaction();

            // 1. طلب التوليد من Gemini
            $generatedDietData = $this->geminiService->generatePrivateDiet($userData, $availableMeals);

            // 2. حفظ الحمية في جدول diets
            $newDiet = new Diet;
            $newDiet->name = $generatedDietData['name'] ?? 'حمية خاصة';
            $newDiet->description = $generatedDietData['description'] ?? 'تم توليدها بواسطة الذكاء الاصطناعي';
            $newDiet->is_public = false; // حمية خاصة
            $newDiet->nutritionist_id = null; // لا يوجد أخصائي، أو يمكن وضع ID مستخدم نظام
            $newDiet->warning = $generatedDietData['warning'] ?? null;
            $newDiet->advice = $generatedDietData['advice'] ?? null;
            $newDiet->save();

            // 3. ربط الوجبات بالحمية
            if (! empty($generatedDietData['suggested_meal_ids'])) {
                // التأكد من أن الوجبات موجودة
                $validMealIds = \App\Models\Meal::whereIn('meals_id', $generatedDietData['suggested_meal_ids'])
                    ->pluck('meals_id');

                $newDiet->meals()->attach($validMealIds);
            }

            // 4. ربط الحمية بالمستخدم في جدول client_diets (أو diets المخصصة)
            // *حسب المودل Client::diets()*
            $client->diets()->attach($newDiet->diets_id);

            // 5. تعيينها كـ "الحمية المتبعة" الحالية
            $client->diets_id = $newDiet->diets_id;
            $client->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم إنشاء الحمية الخاصة وتعيينها لك بنجاح.',
                'data' => $newDiet->load('meals'), // إرجاع الحمية مع وجباتها
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Private Diet Generation Error: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'فشل إنشاء الحمية الخاصة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 4: تعيين حمية للمستخدم (يدوياً)
     * POST /api/user/set-diet
     */
    public function setDiet(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'diet_id' => 'required|exists:diets,diets_id',
        ]);

        $user = $request->user();
        $client = $user->client;

        if (! $client) {
            return response()->json(['status' => 'error', 'message' => 'Client record not found'], 404);
        }

        // هل يجب التحقق من أن الحمية public أو مرتبطة به؟
        // نعم،، المستخدم لا يمكنه اتباع حمية خاصة بغيره
        $diet = Diet::find($request->diet_id);

        if (! $diet->is_public) {
            // تحقق إذا كانت مرتبطة به في client_diets
            $hasAccess = $client->diets()->where('diets.diets_id', $diet->diets_id)->exists();
            if (! $hasAccess) {
                return response()->json(['status' => 'error', 'message' => 'غير مصرح لك باتباع هذه الحمية الخاصة'], 403);
            }
        }

        $client->diets_id = $diet->diets_id;
        $client->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تعيين الحمية المتبعة بنجاح',
            'diet' => $diet,
        ]);
    }

    // Helper: تجهيز البيانات من المودل للصيغة المطلوبة للـ Service
    private function prepareUserData($client): array
    {
        $age = $client->bodyData->birth_date ? \Carbon\Carbon::parse($client->bodyData->birth_date)->age : 30;

        return [
            'age' => $age,
            'gender' => $client->bodyData->sex,
            'weight' => $client->bodyData->weight_kg,
            'height' => $client->bodyData->height_cm,
            'activity_level' => $client->lifestyle->activity_level ?? 'sedentary',
            'diseases' => $client->chronicDiseases->pluck('chronic_diseases')->toArray(),
            'allergies' => $client->allergies->pluck('allergies')->toArray(),
        ];
    }
}
