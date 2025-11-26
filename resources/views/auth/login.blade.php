<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - MealMate</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- إذا كنت تستخدم ملف CSS خارجي -->
    <link rel="stylesheet" href="{{ asset('css/users.css') }}"> 
    <style>
        /* أكواد الـ CSS الخاصة بك هنا */
        /* ... */
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">🍽️</div>
                <h1 class="login-title">MealMate</h1>
                <p class="login-subtitle">Sign in to your account</p>
            </div>
<form class="login-form" id="loginForm" method="POST" action="{{ route('login') }}">
        @csrf {{-- هذا مهم جداً للمصادقة --}}
        
        @error('email')
            <div class="alert alert-danger" style="margin-bottom: 10px; font-size: 14px;">
                البريد الإلكتروني أو كلمة المرور غير صحيحة.
            </div>
        @enderror

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required class="@error('email') is-invalid @enderror">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required class="@error('password') is-invalid @enderror">
        </div>

        <div class="remember-forgot">
            <label class="remember-me">
                <input type="checkbox" name="remember">
                Remember me
            </label>
            <a href="{{ url('/forgetpassword') }}" class="forgot-password">Forgot Password?</a>
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
    </form>
            <div class="change-password-link">
                {{-- ===== ⭐️ تم التعديل هنا ⭐️ ===== --}}
                <a href="{{ url('/changepassword') }}">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </div>

        
            <div class="login-footer">
                {{-- ===== ⭐️ تم التعديل هنا ⭐️ ===== --}}
                <a href="{{ url('/') }}">← Back to Home</a>
            </div>
        </div>
    </div>

 
</body>
</html>


