<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * التحقق من وجود المستخدم
     */
    public function checkExistence(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $identifier = $request->identifier;

        $exists = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'User exists.',
                'registered' => true,
            ], 200);
        }

        return response()->json([
            'message' => 'User not registered.',
            'registered' => false,
        ], 404);
    }

    /**
     * طلب رمز تحقق (Placeholder implementation)
     */
    public function requestCode(Request $request)
    {
        $request->validate(['identifier' => 'required|string']);
        $identifier = $request->identifier;

        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (! $user) {
            return response()->json([
                'message' => 'المستخدم غير مسجل.',
            ], 404);
        }

        // Logic to send OTP would go here

        return response()->json([
            'message' => 'تم إرسال رمز التحقق بنجاح إلى '.$identifier,
        ], 200);
    }

    /**
     * التحقق من رمز التحقق (Placeholder)
     */
    public function verifyCode(Request $request)
    {
        // Placeholder for verifyCode if needed by routes
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * تسجيل الدخول باستخدام كلمة المرور
     */
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->identifier;
        $password = $request->password;

        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الاعتماد غير صحيحة (المستخدم غير موجود)',
            ], 401);
        }

        if (! Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'كلمة المرور غير صحيحة',
            ], 401);
        }

        // Check account state
        if ($user->account_state !== 'Active' && $user->account_state !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'الحساب غير نشط',
            ], 403);
        }

        // إنشاء Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'user' => [
                    'user_id' => $user->user_id,
                    'name' => $user->Fname.' '.$user->Lname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }
}
