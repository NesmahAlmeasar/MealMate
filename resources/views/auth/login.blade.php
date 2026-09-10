<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MealMate - تسجيل الدخول</title>
    
    {{-- Unified Design System (for Fonts & Basics) --}}
    <link rel="stylesheet" href="{{ asset('css/unified-design.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Primary Colors */
        /* Primary Colors - Mapped to Unified Design System */
        :root {
            /* Using global CSS variables from unified-design.css */
            --apple-green: var(--olive-medium); /* Was Light Apple Green, now Olive Medium */
            --olive-green: var(--olive-dark);   /* Was Medium Olive Green, now Olive Dark */
            --dark-olive: #3f5222;              /* Darker shade for hover, manually calculated from Olive Dark */
            --light-bg: var(--bg-gray);
            --white: var(--bg-white);
            --text-color: var(--text-dark);
            --border-color: var(--border-color);
            --box-shadow: var(--shadow-lg);
        }

        /* General Styling */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Cairo', sans-serif !important;
            background-color: var(--white);
            color: var(--text-color);
            text-align: right; /* RTL */
            direction: rtl;
            overflow-x: hidden;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            z-index: 10;
        }

        /* Login/Profile Boxes Common Styling */
        .login-box {
            background-color: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 30px;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            text-align: center;
            z-index: 20;
            position: relative;
        }

        /* Colored Doctor Avatar */
        .doctor-avatar-colored {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 20px auto;
            background-color: #E8F5E9;
            border: 3px solid var(--olive-green);
            background-image: url('{{ asset("images/doctor_avatar.png") }}');
            background-size: cover;
            background-position: center;
        }

        /* Login Screen */
        .input-group {
            margin-bottom: 15px;
            text-align: right;
        }

        .input-group input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            font-size: 1rem;
            text-align: center;
            outline: none;
            transition: border-color 0.3s;
            font-family: 'Cairo', sans-serif;
        }

        .input-group input:focus {
            border-color: var(--apple-green);
        }

        .login-button {
            background-color: var(--olive-green);
            color: var(--white);
            border: none;
            padding: 10px 30px;
            border-radius: 20px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 60%;
            margin-top: 10px;
            font-family: 'Cairo', sans-serif;
        }

        .login-button:hover {
            background-color: var(--dark-olive);
        }

        .signup-link {
            display: block;
            margin-top: 15px;
            color: var(--olive-green);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .error-msg {
            color: #d32f2f;
            font-size: 0.85rem;
            margin-top: 5px;
            text-align: center;
        }

        /* Surrounding Elements */
        .surrounding-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 1;
        }

        .icon-element {
            position: absolute;
            font-size: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-element i {
            margin: 0 5px;
        }

        .doctor-image {
            position: absolute;
            width: 100px;
            height: 100px;
            background-image: url('{{ asset("images/doctor_full.png") }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Positioning for RTL (Flipped Left/Right) */
        .icon-top-left { top: 15%; right: 10%; } /* Flipped */
        .icon-top-right { top: 20%; left: 10%; } /* Flipped */
        .icon-mid-left { top: 45%; right: 5%; } /* Flipped */
        .icon-mid-right { top: 50%; left: 5%; } /* Flipped */
        .doctor-bottom-left { bottom: 10%; right: 15%; } /* Flipped */
        .icon-bottom-center { bottom: 5%; left: 50%; transform: translateX(-50%); } /* Center remains center */
        .doctor-bottom-right { bottom: 15%; left: 15%; } /* Flipped */

        /* Responsive Design */
        @media (min-width: 768px) {
            .login-box { max-width: 500px; }
            .icon-element { font-size: 3rem; }
            .doctor-image { width: 120px; height: 120px; }
        }

        @media (min-width: 1024px) {
            .login-box { max-width: 450px; }
            .icon-element { font-size: 3.5rem; }
            .doctor-image { width: 150px; height: 150px; }
        }

        @media (max-width: 500px) {
            .surrounding-elements { display: none; }
        }
    </style>
</head>
<body>
    <div class="container login-page">
        <div class="login-box">
            <div class="profile-icon-large doctor-avatar-colored">
                <!-- Avatar image set via CSS -->
            </div>
            
            <h2 style="margin-bottom: 20px; color: var(--olive-green); font-weight: bold;">تسجيل الدخول</h2>

            {{-- Validation Errors --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="input-group">
                    <input type="text" id="email" name="email" placeholder="البريد الإلكتروني" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="كلمة المرور" required>
                    @error('password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>
<BR></BR>
                <button type="submit" class="login-button">دخول</button>
            </form>

          
        </div>

        <!-- Surrounding Icons and Doctor Images -->
        <div class="surrounding-elements">
            <!-- Icon 1: Food List -->
            <div class="icon-element icon-top-left">
                <i class="fas fa-clipboard-list" style="color: var(--olive-dark);"></i>
                <i class="fas fa-apple-alt" style="color: var(--olive-light);"></i>
            </div>
            <!-- Icon 2: Heart Health -->
            <div class="icon-element icon-top-right">
                <i class="fas fa-heartbeat" style="color: var(--red-accent);"></i>
                <i class="fas fa-apple-alt" style="color: var(--olive-medium);"></i>
            </div>
            <!-- Icon 3: Add User -->
            <div class="icon-element icon-mid-left">
                <i class="fas fa-user-plus" style="color: var(--blue-primary);"></i>
                <i class="fas fa-apple-alt" style="color: var(--olive-light);"></i>
            </div>
            <!-- Icon 4: Checkmark -->
            <div class="icon-element icon-mid-right">
                <i class="fas fa-check-circle" style="color: var(--green-success);"></i>
            </div>
            <!-- Doctor Image 1 (Bottom Left) -->
            <div class="doctor-image doctor-bottom-left">
                <!-- Placeholder for Doctor Image -->
            </div>
            <!-- Icon 5: Leaf/Nature -->
            <div class="icon-element icon-bottom-center">
                <i class="fas fa-leaf" style="color: var(--olive-dark);"></i>
            </div>
            <!-- Doctor Image 2 (Bottom Right) -->
            <div class="doctor-image doctor-bottom-right">
                <!-- Placeholder for Doctor Image -->
            </div>
        </div>
    </div>
</body>
</html>
