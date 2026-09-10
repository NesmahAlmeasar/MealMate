<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // التحقق من تسجيل ال دخول
        if (! $request->user()) {
            return redirect()->route('login')
                ->with('error', 'يجب تسجيل الدخول أولاً');
        }

        // التحقق من امتلاك المستخدم لأي من الأدوار المطلوبة
        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        // إذا لم يملك أي من الأدوار المطلوبة
        abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
    }
}
