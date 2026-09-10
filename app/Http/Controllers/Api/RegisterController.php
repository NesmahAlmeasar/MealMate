<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Allergy;
use App\Models\BodyData;
use App\Models\ChronicDisease;
use App\Models\Client;
use App\Models\Lifestyle;
use App\Models\MedicalRecord;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * تسجيل حساب جديد عبر البريد الإلكتروني
     */
    public function registerWithEmail(Request $request)
    {
        // 1. التحقق من البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 2. إنشاء المستخدم في جدول users
            $user = User::create([
                'Fname' => $request->first_name,
                'Lname' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => null,
                'photo_url' => 'default_avatar.png', // صورة افتراضية
                'account_state' => 'Active',
            ]);

            // 3. تم نقل إنشاء سجل المستخدم إلى User Observer تلقائياً
            // $client = Client::create([...]);

            // 4. ربط المستخدم بدور "Client"
            $clientRole = Role::where('name', 'Client')->first();
            if ($clientRole) {
                $user->roles()->attach($clientRole->role_id);
                // Ensure Client record exists (Robust Sync)
                $user->load('roles');
                $user->ensureRoleRecords();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الحساب بنجاح',
                'data' => [
                    'user_id' => $user->user_id,
                    'email' => $user->email,
                    'full_name' => $user->Fname.' '.$user->Lname,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الحساب',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تسجيل حساب جديد عبر رقم الهاتف
     */
    public function registerWithPhone(Request $request)
    {
        // 1. التحقق من البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 2. إنشاء المستخدم في جدول users
            $user = User::create([
                'Fname' => $request->first_name,
                'Lname' => $request->last_name,
                'email' => $request->phone.'@mealmate.app', // بريد افتراضي
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'photo_url' => 'default_avatar.png',
                'account_state' => 'Active',
            ]);

            // 3. تم نقل إنشاء سجل المستخدم إلى User Observer تلقائياً
            // $client = Client::create([...]);

            // 4. ربط المستخدم بدور "Client"
            $clientRole = Role::where('name', 'Client')->first();
            if ($clientRole) {
                $user->roles()->attach($clientRole->role_id);
                // Ensure Client record exists (Robust Sync)
                $user->load('roles');
                $user->ensureRoleRecords();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الحساب بنجاح',
                'data' => [
                    'user_id' => $user->user_id,
                    'phone' => $user->phone,
                    'full_name' => $user->Fname.' '.$user->Lname,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الحساب',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * حفظ بيانات الجسم
     */
    public function saveBodyData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
            'birth_date' => 'required|date',
            'height_cm' => 'required|numeric|min:0',
            'weight_kg' => 'required|numeric|min:0',
            'sex' => 'required|in:Male,Female',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // التحقق من وجود Client
            $client = Client::find($request->user_id);
            if (! $client) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير موجود',
                ], 404);
            }

            // حفظ أو تحديث بيانات الجسم
            $bodyData = BodyData::updateOrCreate(
                ['clients_id' => $request->user_id],
                [
                    'birth_date' => $request->birth_date,
                    'height_cm' => $request->height_cm,
                    'weight_kg' => $request->weight_kg,
                    'sex' => $request->sex,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ بيانات الجسم بنجاح',
                'data' => $bodyData,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ البيانات',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * حفظ بيانات نمط الحياة
     */
    public function saveLifestyleData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
            'smoking' => 'required|boolean',
            'activity_level' => 'required|string|max:50',
            'sleeping_hours' => 'required|numeric|min:0|max:24',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $client = Client::find($request->user_id);
            if (! $client) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير موجود',
                ], 404);
            }

            $lifestyle = Lifestyle::updateOrCreate(
                ['clients_id' => $request->user_id],
                [
                    'smoking' => $request->smoking,
                    'activity_level' => $request->activity_level,
                    'sleeping_hours' => $request->sleeping_hours,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ بيانات نمط الحياة بنجاح',
                'data' => $lifestyle,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ البيانات',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * جلب خيارات البيانات الصحية (الأمراض المزمنة، الحساسيات، الادوية الطبية )
     */
    public function getHealthOptions()
    {
        try {
            $chronicDiseases = ChronicDisease::select('chronic_diseases_id as id', 'chronic_diseases as name')->get();
            $allergies = Allergy::select('allergies_id as id', 'allergies as name')->get();
            $medicalRecords = MedicalRecord::select('medical_record_id as id', 'medical_record as name')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'chronic_diseases' => $chronicDiseases,
                    'allergies' => $allergies,
                    'medical_records' => $medicalRecords,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * حفظ البيانات الصحية (الأمراض المزمنة، الحساسيات، الادوية الطبية )
     */
    public function saveHealthData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
            'chronic_disease_ids' => 'nullable|array',
            'chronic_disease_ids.*' => 'exists:chronic_diseases,chronic_diseases_id',
            'allergy_ids' => 'nullable|array',
            'allergy_ids.*' => 'exists:allergies,allergies_id',
            'medical_record_ids' => 'nullable|array', // Updated
            'medical_record_ids.*' => 'exists:medical_record,medical_record_id', // Updated
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $client = Client::find($request->user_id);
            if (! $client) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير موجود',
                ], 404);
            }

            // حفظ الأمراض المزمنة
            if ($request->has('chronic_disease_ids') && is_array($request->chronic_disease_ids)) {
                $client->chronicDiseases()->sync($request->chronic_disease_ids);
            }

            // حفظ الحساسيات
            if ($request->has('allergy_ids') && is_array($request->allergy_ids)) {
                $client->allergies()->sync($request->allergy_ids);
            }

            // حفظ الادوية الطبية  (Updated to sync IDs)
            if ($request->has('medical_record_ids') && is_array($request->medical_record_ids)) {
                $client->medicalRecords()->sync($request->medical_record_ids);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ البيانات الصحية بنجاح',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ البيانات',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
