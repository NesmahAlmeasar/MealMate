<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MealMate - Welcome Screen</title>
    
    {{-- قمنا بجلب الأيقونات فقط --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    {{-- ======================================= --}}
    {{-- ⭐️ تم وضع كل الـ CSS هنا بالداخل ⭐️ --}}
    {{-- ======================================= --}}
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* هذا هو لون الخلفية من تصميمك */
            background: linear-gradient(135deg, #F5F9F0 0%, #E9F0E1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .welcome-container {
            position: relative;
            width: 100%;
            max-width: 800px; /* حجم تقريبي للبطاقة */
        }
        .content-box {
            display: flex;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden; /* للحفاظ على الحواف الدائرية */
            width: 100%;
            min-height: 350px;
        }
        .logo-section {
            /* الجزء الأيسر باللون الأخضر الفاتح */
            background-color: #E2EACD; 
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .logo-section h1 {
            font-size: 36px;
            font-weight: 700;
            color: #6B8E23; /* لون أخضر زيتوني داكن */
        }
        .slogan-section {
            /* الجزء الأيمن الأبيض */
            background-color: #ffffff;
            flex: 1.5; /* إعطاء مساحة أكبر للنص */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            text-align: center;
        }
        .slogan-section p {
            font-size: 26px;
            font-style: italic;
            color: #333;
            margin-bottom: 25px;
            line-height: 1.4;
        }
        .slogan-section .fa-heart {
            font-size: 30px;
            color: #6B8E23; /* لون القلب الأخضر */
        }
        .next-button {
            /* زر السهم الأخضر */
            position: absolute;
            bottom: -20px;
            right: -20px;
            width: 60px;
            height: 60px;
            background-color: #6B8E23; /* لون أخضر */
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, background-color 0.3s ease;
        }
        .next-button:hover {
            transform: scale(1.1);
            background-color: #556B1F; /* لون أغمق عند التأشير */
        }

        /* لجعله متجاوباً مع الجوال */
        @media (max-width: 768px) {
            .content-box {
                flex-direction: column;
                min-height: auto;
            }
            .slogan-section p {
                font-size: 22px;
            }
            .next-button {
                bottom: -25px;
                right: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="content-box">
            <div class="logo-section">
                <h1>MealMate</h1>
            </div>
            <div class="slogan-section">
                <p>You are here to make your life better from food</p>
                <i class="fas fa-heart"></i>
            </div>
        </div>

        {{-- ======================================= --}}
        {{-- ⭐️ تم إصلاح الرابط هنا ⭐️ --}}
        {{-- ======================================= --}}
        {{-- هذا الرابط الآن يشير إلى المسار الذي طلبته --}}
        <a href="{{ url('/login') }}" class="next-button" aria-label="Go to Login Page">
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</body>
</html>