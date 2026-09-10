<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Diet;
use App\Models\Meal;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DietController extends Controller
{
    /**
     * Display a listing of diets
     */
    public function index()
    {
        $user = Auth::user();

        // الحميات العامة للجميع
        $publicDiets = Diet::with(['meals', 'nutritionist'])
            ->where('is_public', true)
            ->latest()
            ->get();

        $privateDiets = collect();

        // إضافة الحميات الخاصة حسب الدور
        if ($user->hasRole('Specialist') || $user->hasRole('Nutrition Manager')) {
            // الحميات الخاصة التي أنشأها الأخصائي
            $privateDiets = Diet::with(['meals', 'nutritionist'])
                ->where('is_public', false)
                ->where('nutritionist_id', $user->user_id)
                ->latest()
                ->get();
        }

        // دمج الحميات
        $diets = $publicDiets->merge($privateDiets);

        return view('shared.diets', compact('diets'));
    }

    /**
     * Display the specified diet
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $diet = Diet::with(['meals.category', 'meals.ingredients', 'nutritionist'])
            ->findOrFail($id);

        // Check if user has access (public OR owner OR admin/manager)
        $hasAccess = $diet->is_public ||
                    ($user->user_id == $diet->nutritionist_id) ||
                    ($user->hasRole('Admin') || $user->hasRole('Nutrition Manager') || $user->hasRole('Restaurant Manager'));

        if (! $hasAccess) {
            abort(403, 'ليس لديك صلاحية لعرض هذه الحمية');
        }

        // Fetch all approved meals for the "Add Meal" modal
        $allMeals = Meal::with('category')->where('state', 'approved')->get();

        return view('shared.diet-details', compact('diet', 'allMeals'));
    }

    /**
     * Sync meals for a specific diet
     */
    public function syncMeals(Request $request, $id)
    {
        $user = Auth::user();
        $diet = Diet::findOrFail($id);

        // Check permission (Owner or Admin/Manager)
        $canEdit = ($user->user_id == $diet->nutritionist_id) ||
                   ($user->hasRole('Admin') || $user->hasRole('Nutrition Manager'));

        if (! $canEdit) {
            abort(403, 'ليس لديك صلاحية لتعديل وجبات هذه الحمية');
        }

        $request->validate([
            'meal_ids' => 'array',
            'meal_ids.*' => 'exists:meals,meals_id',
        ]);

        $diet->meals()->sync($request->meal_ids ?? []);

        return back()->with('success', 'تم تحديث قائمة الوجبات بنجاح');
    }

    /**
     * Show the form for creating a new diet
     */
    /**
     * Show the form for creating a new diet
     */
    public function create()
    {
        $user = Auth::user();

        // التحقق من الصلاحية
        if (! $user->hasRole('Specialist') && ! $user->hasRole('Nutrition Manager')) {
            abort(403, 'ليس لديك صلاحية لإنشاء حميات');
        }

        $meals = Meal::with('category')->where('state', 'approved')->get();

        // Get all active consultations for this nutritionist
        $activeConsultations = \App\Models\Consultation::where('nutritionist_id', $user->user_id)
            ->where('status', 'active')
            ->where('end_time', '>', now())
            ->get();

        // Extract unique client IDs (user_ids) from consultations
        $clientUserIds = $activeConsultations->pluck('client_id')->unique();

        // Get Client records for these user_ids
        $clients = Client::whereIn('clients_id', $clientUserIds)
            ->with('user')
            ->get();

        $canCreatePublic = $user->hasRole('Nutrition Manager');

        return view('shared.diet-create', compact('meals', 'clients', 'canCreatePublic'));
    }

    /**
     * Store a newly created diet
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // ⭐️ Custom Validation Logic for Private Diets
        $isPublic = $request->boolean('is_public');
        if (! $isPublic && empty($request->clients)) {
            return back()->withErrors(['clients' => 'يجب اختيار عميل واحد على الأقل للحمية الخاصة'])->withInput();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:diets,name', // ⭐️ Unique Validation
            'description' => 'required|string', // ⭐️ Required as per request (no empty data)
            'warning' => 'nullable|string',
            'advice' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_public' => 'nullable|boolean',
            'meals' => 'nullable|array',
            'meals.*' => 'exists:meals,meals_id',
            'clients' => 'nullable|array',
            'clients.*' => 'exists:clients,clients_id',
            'restrictions' => 'nullable|array',
            'restrictions.*.restriction' => 'nullable|string',
            'restrictions.*.field_name' => 'required|string',
            'restrictions.*.operator' => 'required|string',
            'restrictions.*.value' => 'required|numeric',
        ]);

        // التحقق من صلاحية إنشاء حمية عامة
        if ($request->is_public && ! $user->hasRole('Nutrition Manager')) {
            return back()->withErrors(['is_public' => 'ليس لديك صلاحية لإنشاء حميات عامة']);
        }

        // إنشاء الحمية
        $diet = Diet::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'warning' => $validated['warning'],
            'advice' => $validated['advice'],
            'is_public' => $request->is_public ?? false,
            'nutritionist_id' => $user->user_id,
            'photo_url' => $request->hasFile('photo')
                ? $request->file('photo')->store('diets', 'public')
                : null,
        ]);

        // ربط الوجبات
        if (! empty($validated['meals'])) {
            $diet->meals()->attach($validated['meals']);
        }

        // ربط العملاء (للحميات الخاصة فقط)
        if (! $diet->is_public && ! empty($validated['clients'])) {
            $diet->clients()->attach($validated['clients']);
        }

        // حفظ القيود
        if (! empty($validated['restrictions'])) {
            foreach ($validated['restrictions'] as $restriction) {
                if (! empty($restriction['field_name'])) {
                    $diet->restrictions()->create($restriction);
                }
            }
        }

        return redirect()->route('shared.diets')->with('success', 'تم إنشاء الحمية بنجاح');
    }

    /**
     * Show the form for editing the specified diet
     */
    public function edit($id)
    {
        $user = Auth::user();
        $diet = Diet::with(['meals', 'clients', 'restrictions'])->findOrFail($id);

        // التحقق من الصلاحية
        $canEdit = false;

        if ($user->hasRole('Nutrition Manager')) {
            // يمكنه تعديل جميع الحميات العامة وحمياته الخاصة
            $canEdit = $diet->is_public || $diet->nutritionist_id == $user->user_id;
        } elseif ($user->hasRole('Specialist')) {
            // يمكنه تعديل حمياته الخاصة فقط
            $canEdit = ! $diet->is_public && $diet->nutritionist_id == $user->user_id;
        }

        if (! $canEdit) {
            abort(403, 'ليس لديك صلاحية لتعديل هذه الحمية');
        }

        $meals = Meal::with('category')->where('state', 'approved')->get();

        // Get associated client IDs from the diet
        $associatedClientIds = $diet->clients->pluck('clients_id');

        // Get all active consultations for this nutritionist
        $activeConsultations = \App\Models\Consultation::where('nutritionist_id', $user->user_id)
            ->where('status', 'active')
            ->where('end_time', '>', now())
            ->get();

        // Extract unique client IDs (user_ids) from consultations
        $clientUserIds = $activeConsultations->pluck('client_id')->unique();

        // Merge with associated clients to keep existing selections
        $allClientIds = $clientUserIds->merge($associatedClientIds)->unique();

        // Get Client records
        $clients = Client::whereIn('clients_id', $allClientIds)
            ->with('user')
            ->get();

        $canCreatePublic = $user->hasRole('Nutrition Manager');

        return view('shared.diet-edit', compact('diet', 'meals', 'clients', 'canCreatePublic'));
    }

    /**
     * Update the specified diet
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $diet = Diet::findOrFail($id);

        // التحقق من الصلاحية
        $canEdit = false;

        if ($user->hasRole('Nutrition Manager')) {
            $canEdit = $diet->is_public || $diet->nutritionist_id == $user->user_id;
        } elseif ($user->hasRole('Specialist')) {
            $canEdit = ! $diet->is_public && $diet->nutritionist_id == $user->user_id;
        }

        if (! $canEdit) {
            abort(403, 'ليس لديك صلاحية لتعديل هذه الحمية');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'warning' => 'nullable|string',
            'advice' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_public' => 'nullable|boolean',
            'meals' => 'nullable|array',
            'meals.*' => 'exists:meals,meals_id',
            'clients' => 'nullable|array',
            'clients.*' => 'exists:clients,clients_id',
            'restrictions' => 'nullable|array',
            'restrictions.*.restriction' => 'nullable|string',
            'restrictions.*.field_name' => 'required|string',
            'restrictions.*.operator' => 'required|string',
            'restrictions.*.value' => 'required|numeric',
        ]);

        // التحقق من صلاحية تغيير نوع الحمية إلى عامة
        if ($request->is_public && ! $user->hasRole('Nutrition Manager')) {
            return back()->withErrors(['is_public' => 'ليس لديك صلاحية لجعل الحمية عامة']);
        }

        // تحديث البيانات الأساسية
        $diet->name = $validated['name'];
        $diet->description = $validated['description'];
        $diet->warning = $validated['warning'];
        $diet->advice = $validated['advice'];
        $diet->is_public = $request->is_public ?? false;

        // تحديث الصورة
        if ($request->hasFile('photo')) {
            if ($diet->photo_url) {
                Storage::disk('public')->delete($diet->photo_url);
            }
            $diet->photo_url = $request->file('photo')->store('diets', 'public');
        }

        $diet->save();

        // تحديث الوجبات
        if (isset($validated['meals'])) {
            $diet->meals()->sync($validated['meals']);
        }

        // تحديث العملاء (للحميات الخاصة فقط)
        if (! $diet->is_public && isset($validated['clients'])) {
            $diet->clients()->sync($validated['clients']);
        } elseif ($diet->is_public) {
            // إذا أصبحت الحمية عامة، إزالة جميع ارتباطات العملاء
            $diet->clients()->detach();
        }

        // تحديث القيود
        $diet->restrictions()->delete();
        if (! empty($validated['restrictions'])) {
            foreach ($validated['restrictions'] as $restriction) {
                if (! empty($restriction['field_name'])) {
                    $diet->restrictions()->create($restriction);
                }
            }
        }

        return redirect()->route('shared.diets')->with('success', 'تم تحديث الحمية بنجاح');
    }

    /**
     * Remove the specified diet
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $diet = Diet::findOrFail($id);

        // التحقق من الصلاحية
        $canDelete = false;

        if ($user->hasRole('Nutrition Manager')) {
            $canDelete = $diet->is_public || $diet->nutritionist_id == $user->user_id;
        } elseif ($user->hasRole('Specialist')) {
            $canDelete = ! $diet->is_public && $diet->nutritionist_id == $user->user_id;
        }

        if (! $canDelete) {
            abort(403, 'ليس لديك صلاحية لحذف هذه الحمية');
        }

        // حذف الصورة
        if ($diet->photo_url) {
            Storage::disk('public')->delete($diet->photo_url);
        }

        $diet->delete();

        return redirect()->route('shared.diets')->with('success', 'تم حذف الحمية بنجاح');
    }

    /**
     * AI Suggest Meals based on restrictions
     */
    public function aiSuggestMeals(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'description' => 'nullable|string',
                'restrictions' => 'required|array',
            ]);

            $meals = Meal::with('category')->where('state', 'approved')->get();

            // بناء prompt للذكاء الاصطناعي
            $prompt = "أنت خبير تغذية. لدي حمية اسمها '{$validated['name']}'";

            if (! empty($validated['description'])) {
                $prompt .= " ووصفها: {$validated['description']}";
            }

            $prompt .= "\n\nالقيود الغذائية:\n";
            foreach ($validated['restrictions'] as $restriction) {
                $prompt .= "- {$restriction['restriction']}: {$restriction['field_name']} {$restriction['operator']} {$restriction['value']}\n";
            }

            $prompt .= "\n\nقائمة الوجبات المتاحة:\n";
            foreach ($meals as $meal) {
                $prompt .= "ID: {$meal->meals_id}, الاسم: {$meal->name}, السعرات: {$meal->calories}, البروتين: {$meal->protein_g}g, الكربوهيدرات: {$meal->carbs_g}g, الدهون: {$meal->fat_g}g\n";
            }

            $prompt .= "\n\nاقترح أفضل الوجبات التي تتناسب مع هذه القيود. أرجع فقط قائمة بأرقام IDs للوجبات المقترحة بصيغة JSON مثل: {\"meal_ids\": [1, 5, 8], \"reason\": \"السبب\"}";

            $result = Gemini::generativeModel('gemini-flash-latest')->generateContent($prompt);
            $response = $result->text();

            // استخراج JSON من الاستجابة
            preg_match('/\{[^}]+\}/', $response, $matches);

            if (! empty($matches)) {
                $data = json_decode($matches[0], true);

                if ($data && isset($data['meal_ids'])) {
                    $suggestedMeals = Meal::whereIn('meals_id', $data['meal_ids'])->get();

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'meals' => $suggestedMeals,
                            'reason' => $data['reason'] ?? 'تم اختيار الوجبات بناءً على القيود المحددة',
                        ],
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'لم يتمكن الذكاء الاصطناعي من اقتراح وجبات مناسبة',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: '.$e->getMessage(),
            ], 500);
        }
    }
}
