<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BodyData;
use App\Models\Client;
use App\Models\Lifestyle;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * عرض الملف الشخصي
     * GET /api/profile
     */
    public function show(): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            $client = Client::with(['bodyData', 'lifestyle', 'chronicDiseases', 'allergies', 'diet'])
                ->find($user->user_id);

            $profileData = [
                'user' => [
                    'user_id' => $user->user_id,
                    'first_name' => $user->Fname,
                    'last_name' => $user->Lname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'photo_url' => $user->photo_url,
                ],
                'body_data' => $client && $client->bodyData ? [
                    'birth_date' => $client->bodyData->birth_date,
                    'height_cm' => $client->bodyData->height_cm,
                    'weight_kg' => $client->bodyData->weight_kg,
                    'sex' => $client->bodyData->sex,
                ] : null,
                'lifestyle' => $client && $client->lifestyle ? [
                    'smoking' => $client->lifestyle->smoking,
                    'activity_level' => $client->lifestyle->activity_level,
                    'sleeping_hours' => $client->lifestyle->sleeping_hours,
                ] : null,
                'health_info' => $client ? [
                    'chronic_diseases' => $client->chronicDiseases->map(function ($disease) {
                        return [
                            'id' => $disease->chronic_diseases_id,
                            'name' => $disease->chronic_diseases,
                        ];
                    }),
                    'allergies' => $client->allergies->map(function ($allergy) {
                        return [
                            'id' => $allergy->allergies_id,
                            'name' => $allergy->allergies,
                        ];
                    }),
                ] : null,
                // [NEW] إضافة معلومات الحمية
                'diet_id' => $client ? $client->diets_id : null,
                'food_type' => ($client && $client->diet) ? $client->diet->name : null,
                'diet' => ($client && $client->diet) ? $client->diet : null,
            ];

            // Check profile completion
            $hasBodyData = $client && $client->bodyData !== null;
            $hasLifestyle = $client && $client->lifestyle !== null;
            $hasHealthInfo = $client && ($client->chronicDiseases->count() > 0 || $client->allergies->count() > 0);

            $profileData['profile_complete'] = [
                'is_complete' => $hasBodyData && $hasLifestyle,
                'has_body_data' => $hasBodyData,
                'has_lifestyle' => $hasLifestyle,
                'has_health_info' => $hasHealthInfo,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $profileData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب الملف الشخصي',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تحديث الملف الشخصي
     * PUT /api/profile
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            // استخراج البيانات من الـ objects المتداخلة
            $personalInfo = $request->input('personal_info', []);
            $bodyData = $request->input('body_data', []);
            $lifestyleData = $request->input('lifestyle', []);

            // Validation
            $validator = \Illuminate\Support\Facades\Validator::make([
                'first_name' => $personalInfo['first_name'] ?? null,
                'last_name' => $personalInfo['last_name'] ?? null,
                'email' => $personalInfo['email'] ?? null,
                'phone' => $personalInfo['phone'] ?? null,
                'birth_date' => $bodyData['birth_date'] ?? null,
                'height_cm' => $bodyData['height_cm'] ?? null,
                'weight_kg' => $bodyData['weight_kg'] ?? null,
                'sex' => $bodyData['sex'] ?? null,
                'smoking' => $lifestyleData['smoking'] ?? null,
                'activity_level' => $lifestyleData['activity_level'] ?? null,
                'sleeping_hours' => $lifestyleData['sleeping_hours'] ?? null,
            ], [
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,'.$user->user_id.',user_id',
                'phone' => 'nullable|string|max:20',
                'birth_date' => 'nullable|date',
                'height_cm' => 'nullable|numeric|min:0',
                'weight_kg' => 'nullable|numeric|min:0',
                'sex' => 'nullable|in:Male,Female',
                'smoking' => 'nullable|boolean',
                'activity_level' => 'nullable|string|max:50',
                'sleeping_hours' => 'nullable|numeric|min:0|max:24',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            // Update User Info من personal_info
            if (isset($personalInfo['first_name'])) {
                $user->Fname = $personalInfo['first_name'];
            }
            if (isset($personalInfo['last_name'])) {
                $user->Lname = $personalInfo['last_name'];
            }
            if (isset($personalInfo['email'])) {
                $user->email = $personalInfo['email'];
            }
            if (isset($personalInfo['phone'])) {
                $user->phone = $personalInfo['phone'];
            }

            $user->save();
            $user->refresh();

            // Update Body Data من body_data
            if (! empty($bodyData)) {
                $bodyDataFields = [];
                if (isset($bodyData['birth_date'])) {
                    $bodyDataFields['birth_date'] = $bodyData['birth_date'];
                }
                if (isset($bodyData['height_cm'])) {
                    $bodyDataFields['height_cm'] = $bodyData['height_cm'];
                }
                if (isset($bodyData['weight_kg'])) {
                    $bodyDataFields['weight_kg'] = $bodyData['weight_kg'];
                }
                if (isset($bodyData['sex'])) {
                    $bodyDataFields['sex'] = $bodyData['sex'];
                }

                if (! empty($bodyDataFields)) {
                    BodyData::updateOrCreate(
                        ['clients_id' => $user->user_id],
                        $bodyDataFields
                    );
                }
            }

            // Update Lifestyle من lifestyle
            if (! empty($lifestyleData)) {
                $lifestyleFields = [];
                if (isset($lifestyleData['smoking'])) {
                    $lifestyleFields['smoking'] = $lifestyleData['smoking'];
                }
                if (isset($lifestyleData['activity_level'])) {
                    $lifestyleFields['activity_level'] = $lifestyleData['activity_level'];
                }
                if (isset($lifestyleData['sleeping_hours'])) {
                    $lifestyleFields['sleeping_hours'] = $lifestyleData['sleeping_hours'];
                }

                if (! empty($lifestyleFields)) {
                    Lifestyle::updateOrCreate(
                        ['clients_id' => $user->user_id],
                        $lifestyleFields
                    );
                }
            }

            DB::commit();

            // إعادة جلب البيانات المحدثة
            $client = Client::with(['bodyData', 'lifestyle', 'chronicDiseases', 'allergies', 'diet'])->find($user->user_id);

            // Check profile completion after update
            $hasBodyData = $client && $client->bodyData !== null;
            $hasLifestyle = $client && $client->lifestyle !== null;
            $hasHealthInfo = $client && ($client->chronicDiseases->count() > 0 || $client->allergies->count() > 0);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث الملف الشخصي بنجاح',
                'data' => [
                    'user' => [
                        'user_id' => $user->user_id,
                        'first_name' => $user->Fname,
                        'last_name' => $user->Lname,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'photo_url' => $user->photo_url,
                    ],
                    'body_data' => $client && $client->bodyData ? [
                        'birth_date' => $client->bodyData->birth_date,
                        'height_cm' => $client->bodyData->height_cm,
                        'weight_kg' => $client->bodyData->weight_kg,
                        'sex' => $client->bodyData->sex,
                    ] : null,
                    'lifestyle' => $client && $client->lifestyle ? [
                        'smoking' => $client->lifestyle->smoking,
                        'activity_level' => $client->lifestyle->activity_level,
                        'sleeping_hours' => $client->lifestyle->sleeping_hours,
                    ] : null,
                    // [NEW] إضافة معلومات الحمية
                    'diet_id' => $client ? $client->diets_id : null,
                    'food_type' => ($client && $client->diet) ? $client->diet->name : null,
                    'diet' => ($client && $client->diet) ? $client->diet : null,
                ],
                'profile_complete' => [
                    'is_complete' => $hasBodyData && $hasLifestyle,
                    'has_body_data' => $hasBodyData,
                    'has_lifestyle' => $hasLifestyle,
                    'has_health_info' => $hasHealthInfo,
                ],
                'suggest_diet' => ($hasBodyData && $hasLifestyle) ? 'هل تريد أن نقترح لك حمية باستخدام الذكاء الاصطناعي؟' : null,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في تحديث الملف الشخصي',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * الحصول على الحمية الحالية للعميل
     * GET /api/client/diet
     */
    public function getDiet(): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

            $client = Client::find($user->user_id);
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'diet_id' => $client ? $client->diets_id : null,
                    'diet' => $client && $client->diet ? $client->diet : null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * تحديث حمية المستخدم
     * POST /api/client/diet
     */
    public function updateDiet(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

            $request->validate([
                'diet_id' => 'required|exists:diets,diets_id',
            ]);

            $client = Client::firstOrCreate(['clients_id' => $user->user_id]);
            $client->diets_id = $request->diet_id;
            $client->save();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث الحمية بنجاح',
                'data' => ['diet_id' => $client->diets_id]
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * الحصول على موقع المستخدم (الأحدث)
     * GET /api/client/location
     */
    public function getLocation(): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

            $client = Client::find($user->user_id);
            
            if (!$client) {
                return response()->json([
                    'status' => 'success', 
                    'message' => 'No client record found',
                    'data' => null
                ]);
            }

            // نفترض أن الموقع الأحدث هو "موقع المستخدم الحالي"
            $location = $client->locations()->orderBy('created_at', 'desc')->first();

            return response()->json([
                'status' => 'success',
                'data' => $location
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * الحصول على جميع مواقع المستخدم
     * GET /api/client/locations
     */
    public function getAllLocations(): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

            $client = Client::find($user->user_id);
            
            if (!$client) {
                return response()->json([
                    'status' => 'success', 
                    'message' => 'No client record found',
                    'data' => []
                ]);
            }

            $locations = $client->locations()->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'data' => $locations
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * تحديث موقع المستخدم
     * POST /api/client/location
     */
    public function updateLocation(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'description' => 'required|string',
            ]);

            $client = Client::firstOrCreate(['clients_id' => $user->user_id]);

            // التحقق مما إذا كان لديه موقع بالفعل لتحديثه، أو إنشاء جديد
            // هنا سنتبع منطق: نحدث الأخير إذا وجد، وإلا ننشئ جديد
            // ملاحظة: عادة المواقع تكون قائمة عناوين، لكن بناءً على طلب "تعديل الموقع"، سنحدث الأحدث.
            
            $latestLocation = $client->locations()->orderBy('created_at', 'desc')->first();

            if ($latestLocation) {
                $latestLocation->update([
                    'latitude_x' => $request->latitude,
                    'longitude_y' => $request->longitude,
                    'description' => $request->description,
                ]);
                $location = $latestLocation;
                $message = 'تم تحديث الموقع بنجاح';
            } else {
                // إنشاء موقع جديد وربطه
                $location = \App\Models\Location::create([
                    'latitude_x' => $request->latitude,
                    'longitude_y' => $request->longitude,
                    'description' => $request->description,
                ]);
                $client->locations()->attach($location->location_id);
                $message = 'تم إضافة الموقع بنجاح';
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $location
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
