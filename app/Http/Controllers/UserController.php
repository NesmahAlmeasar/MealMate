<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User; // ⭐️ 1. استيراد نموذج Role
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// قد نحتاجه لضمان سلامة العمليات

class UserController extends Controller
{
    public function index(Request $request)
    {
        // ⭐️ Eager Loading of roles
        $query = User::with('roles');

        // ⭐️ Filtering by Role
        if ($request->has('role') && ! empty($request->role)) {
            $roleName = $request->role;
            $query->whereHas('roles', function ($q) use ($roleName) {
                $q->where('name', $roleName);
            });
        }

        $users = $query->paginate(20);
        $allRoles = Role::all();

        return view('admin.users', compact('users', 'allRoles'));
    }

    public function store(Request $request)
    {
        // 1. التحقق من البيانات
        $validatedData = $request->validate([
            'Fname' => 'required|string|max:255', // ⭐️ تحديث
            'Lname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',

            // ⭐️ 2. تعديل التحقق: الآن نحن نتحقق من مصفوفة الأدوار
            'role_names' => ['required', 'array'],
            'role_names.*' => ['exists:roles,name'], // ✅ التحقق من وجود الدور في قاعدة البيانات

            'account_state' => ['required', Rule::in(['Active', 'Pending', 'Banned'])],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPath = null;

        try {
            // 2. رفع الصورة (تضمين الكود السابق المحذوف)
            if ($request->hasFile('photo')) {
                // تخزين الملف في مجلد 'profile_photos' ضمن مجلد 'public'
                $photoPath = $request->file('photo')->store('profile_photos', 'public');
            }

            // 3. إنشاء المستخدم
            $user = User::create([
                'Fname' => $validatedData['Fname'], // ⭐️ تحديث: حفظ Fname
                'Lname' => $validatedData['Lname'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'password' => bcrypt($validatedData['password']), // ✅ تشفير كلمة المرور
                'account_state' => $validatedData['account_state'],
                'photo_url' => $photoPath,
            ]);

            // ⭐️ 4. إضافة منطق ربط الأدوار المتعددة

            // 4.1. البحث عن الأدوار المطلوبة
            $roles = Role::whereIn('name', $validatedData['role_names'])->get();

            if ($roles->isNotEmpty()) {
                // 4.2. ربط المستخدم بالأدوار
                $user->roles()->attach($roles->pluck('role_id'));
                // ⭐️ Ensure related records (Client/Nutritionist) are created
                $user->load('roles'); // Reload roles
                $user->ensureRoleRecords();
            } else {
                // يمكنك إضافة منطق لمعالجة إذا لم يتم العثور على أي دور
                // لكن التحقق في البداية يضمن وجود أدوار صحيحة
            }

            // 5. إعادة التوجيه مع رسالة نجاح
            return redirect()->back()->with('success', 'تمت إضافة المستخدم بنجاح  !');

        } catch (\Exception $e) {
            // معالجة الخطأ
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء الحفظ: '.$e->getMessage()])->withInput();
        }
    }

    // في ملف app/Http/Controllers/UserController.php (لتطبيق حفظ التعديلات)

    // ... (دوال index و store و destroy تبقى كما هي) ...

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 1. التحقق من البيانات (تجاهل المستخدم الحالي في البريد والهاتف)
        $validatedData = $request->validate([
            'Fname' => 'required|string|max:255',
            'Lname' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->user_id, 'user_id')],
            'password' => 'nullable|string|min:8|confirmed',
            'role_names' => ['required', 'array'],
            'role_names.*' => ['exists:roles,name'], // ✅ التحقق من وجود الدور في قاعدة البيانات
            'account_state' => ['required', Rule::in(['Active', 'Pending', 'Banned'])],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. معالجة تحديث الصورة (وحذف القديمة)
        $photoPath = $user->photo_url;
        if ($request->hasFile('photo')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->photo_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->photo_url)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->photo_url);
            }
            // رفع الصورة الجديدة
            $photoPath = $request->file('photo')->store('profile_photos', 'public');
        }

        // 3. تحديث البيانات الأساسية (بما في ذلك Fname و Lname)
        $user->update([
            'Fname' => $validatedData['Fname'],
            'Lname' => $validatedData['Lname'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'account_state' => $validatedData['account_state'],
            'photo_url' => $photoPath,
            // تحديث كلمة المرور فقط إذا تم إدخال قيمة جديدة
            'password' => $request->filled('password') ? bcrypt($validatedData['password']) : $user->password, // ✅ تشفير كلمة المرور
        ]);

        // 4. تحديث الأدوار (Sync)
        $roles = Role::whereIn('name', $validatedData['role_names'])->get();
        if ($roles->isNotEmpty()) {
            $user->roles()->sync($roles->pluck('role_id'));
            // ⭐️ Ensure related records (Client/Nutritionist) are created
            $user->load('roles'); // Reload roles
            $user->ensureRoleRecords();
        } else {
            // إذا تم إرسال مصفوفة فارغة (نظرياً لا يحدث بسبب التحقق required)
            $user->roles()->detach();
        }

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'تم حذف المستخدم بنجاح.');
    }

    // في ملف app/Http/Controllers/UserController.php

    public function edit($id)
    {
        // 1. جلب المستخدم المطلوب مع تحميل أدواره
        $user = User::with('roles')->findOrFail($id);

        // 2. جلب جميع الأدوار
        $allRoles = Role::all();

        // 3. تمرير المتغيرات إلى الـ View
        return view('admin.edit_user', compact('user', 'allRoles'));
    }
    // ⭐️ دالة تحديث بيانات المستخدم (جديدة)
    // في ملف app/Http/Controllers/UserController.php

    public function updateSpecialistInfo(Request $request, $id)
    {
        $request->validate([
            'Academic_level' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $nutritionist = \App\Models\Nutritionist::where('nutritionist_id', $id)->firstOrFail();

        $nutritionist->update([
            'Academic_level' => $request->Academic_level,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'تم تحديث معلومات الأخصائي بنجاح.');
    }

    public function addCertificate(Request $request, $id)
    {
        $request->validate([
            'certificate_type' => 'required|string|max:255',
            'certificate_file' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $nutritionist = \App\Models\Nutritionist::where('nutritionist_id', $id)->firstOrFail();

        $data = [
            'nutritionist_id' => $nutritionist->nutritionist_id,
            'certificate_type' => $request->certificate_type,
        ];

        if ($request->hasFile('certificate_file')) {
            $data['photo_url'] = $request->file('certificate_file')->store('certificates', 'public');
        }

        \App\Models\Certificate::create($data);

        return redirect()->back()->with('success', 'تمت إضافة الشهادة بنجاح.');
    }

    public function deleteCertificate($id)
    {
        $certificate = \App\Models\Certificate::where('certificate_id', $id)->firstOrFail();

        // Delete the file if it exists
        if ($certificate->photo_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($certificate->photo_url)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($certificate->photo_url);
        }

        $certificate->delete();

        return redirect()->back()->with('success', 'تم حذف الشهادة بنجاح.');
    }
}
