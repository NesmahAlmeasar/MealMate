\u003c!DOCTYPE html\u003e
\u003chtml lang="ar" dir="rtl"\u003e
\u003chead\u003e
    \u003cmeta charset="UTF-8"\u003e
    \u003cmeta name="viewport" content="width=device-width, initial-scale=1.0"\u003e
    \u003ctitle\u003eMealMate - مرحباً بك\u003c/title\u003e
    
    {{-- Unified Design System --}}
    \u003clink rel="stylesheet" href="{{ asset('css/unified-design.css') }}"\u003e
    
    {{-- Font Awesome --}}
    \u003clink rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"\u003e
    
    {{-- Arabic Font --}}
    \u003clink rel="preconnect" href="https://fonts.googleapis.com"\u003e
    \u003clink rel="preconnect" href="https://fonts.gstatic.com" crossorigin\u003e
    \u003clink href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet"\u003e
    
    \u003cstyle\u003e
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, var(--olive-very-light) 0%, var(--olive-light) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-xl);
            direction: rtl;
        }
        
        .welcome-container {
            position: relative;
            width: 100%;
            max-width: 800px;
        }
        
        .content-box {
            display: flex;
            background: var(--bg-white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            width: 100%;
            min-height: 350px;
        }
        
        .logo-section {
            background-color: var(--olive-light);
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(2rem, 5vw, 3rem);
        }
        
        .logo-section h1 {
            font-size: clamp(2rem, 4vw, 2.5rem);
            font-weight: 700;
            color: var(--olive-dark);
        }
        
        .slogan-section {
            background-color: var(--bg-white);
            flex: 1.5;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: clamp(2rem, 5vw, 3rem);
            text-align: center;
        }
        
        .slogan-section p {
            font-size: clamp(1.25rem, 3vw, 1.625rem);
            font-style: italic;
            color: var(--text-dark);
            margin-bottom: var(--spacing-xl);
            line-height: 1.4;
        }
        
        .slogan-section .fa-heart {
            font-size: clamp(1.5rem, 3vw, 2rem);
            color: var(--olive-medium);
        }
        
        .next-button {
            position: absolute;
            bottom: -20px;
            left: -20px;
            width: 60px;
            height: 60px;
            background-color: var(--olive-medium);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            text-decoration: none;
            box-shadow: var(--shadow-lg);
            transition: all var(--transition-normal);
        }
        
        .next-button:hover {
            transform: scale(1.1);
            background-color: var(--olive-dark);
        }

        @media (max-width: 768px) {
            .content-box {
                flex-direction: column;
                min-height: auto;
            }
            
            .slogan-section p {
                font-size: clamp(1.125rem, 3vw, 1.375rem);
            }
            
            .next-button {
                bottom: -25px;
                left: 15px;
            }
        }
    \u003c/style\u003e
\u003c/head\u003e
\u003cbody\u003e
    \u003cdiv class="welcome-container"\u003e
        \u003cdiv class="content-box"\u003e
            \u003cdiv class="logo-section"\u003e
                \u003ch1\u003eMealMate\u003c/h1\u003e
            \u003c/div\u003e
            \u003cdiv class="slogan-section"\u003e
                \u003cp\u003eأنت هنا لتجعل حياتك أفضل من خلال الطعام\u003c/p\u003e
                \u003ci class="fas fa-heart"\u003e\u003c/i\u003e
            \u003c/div\u003e
        \u003c/div\u003e

        \u003ca href="{{ url('/login') }}" class="next-button" aria-label="الذهاب إلى صفحة تسجيل الدخول"\u003e
            \u003ci class="fas fa-arrow-left"\u003e\u003c/i\u003e
        \u003c/a\u003e
    \u003c/div\u003e
\u003c/body\u003e
\u003c/html\u003e