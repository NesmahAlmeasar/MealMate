<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Otp; // تأكد من إنشاء مودل Otp إذا لم تقم بذلك
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginOtpMail; // سننشئ هذا لاحقاً

class AuthController extends Controller
{
    // ... (دوال requestCode و verifyCode موجودة) ...

    public function checkExistence(Request $request)
    {
        // 1. التحقق من البيانات المرسلة
        $request->validate([
            'identifier' => 'required|string', // الإيميل أو الهاتف
        ]);

        $identifier = $request->identifier;

        // 2. البحث عن المستخدم بالإيميل أو الهاتف
        $exists = User::where('email', $identifier)
                      ->orWhere('phone', $identifier)
                      ->exists(); // 💡 دالة exists() سريعة جداً

        if ($exists) {
            // 3. إذا كان موجوداً
            return response()->json([
                'message' => 'User exists.',
                'registered' => true
            ], 200);
        }

        // 4. إذا لم يكن موجوداً
        return response()->json([
            'message' => 'User not registered.',
            'registered' => false
        ], 404); 
    }

    // app/Http/Controllers/Api/AuthController.php

// يجب أن تكون دالة requestCode بهذا الشكل تقريباً (أنت لم تشاركها، لذا سأفترض منطقها):
public function requestCode(Request $request)
{
    $request->validate(['identifier' => 'required|string']);
    $identifier = $request->identifier;

    // 1. البحث عن المستخدم
    $user = User::where('email', $identifier)
                 // ............ orWhere('phone', $identifier) إذا كنت تدعم الهاتف أيضاً
                 ->first();

    // 2. التحقق من وجود المستخدم
    if (!$user) {
        // ❌ المستخدم غير موجود. يجب أن يرسل 404 كما تتوقعه Flutter
        return response()->json([
            'message' => 'المستخدم غير مسجل.'
        ], 404);
    }

    // 3. إنشاء وإرسال الرمز (هذا الجزء يتطلب منك كتابته: توليد الرمز وتخزينه في جدول Otp وإرساله)
    // ...
    // ... (منطق إنشاء وإرسال رمز OTP)
    // ...

    // 4. إرجاع نجاح (200) بعد إرسال الرمز
    return response()->json([
        'message' => 'تم إرسال رمز التحقق بنجاح إلى ' . $identifier
    ], 200);
}
}