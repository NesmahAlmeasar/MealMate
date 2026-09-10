<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        if (Auth::check()) {
            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. التحقق من صحة البيانات (الإيميل وكلمة المرور)
        $request->authenticate();

        // 2. تجديد الجلسة
        $request->session()->regenerate();

        // 3. جلب المستخدم الحالي
        $user = $request->user();

        // 4. فحص الأدوار والتوجيه إلى الداش بورد المشتركة
        // إذا كان المستخدم: أخصائي، مدير تغذية، مدير مطعم، أو مدير عام
        if ($user->hasRole('Admin') ||
            $user->hasRole('Specialist') ||
            $user->hasRole('Nutrition Manager') ||
            $user->hasRole('Restaurant Manager')) {

            return redirect()->intended(route('shared.dashboard', absolute: false));
        }

        // 5. إذا كان مستخدم عادي (User/Client) - منع الدخول للويب
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->withErrors([
            'email' => 'عذراً، لا تملك صلاحية الدخول. هذا النظام مخصص للمدراء والأخصائيين فقط.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
