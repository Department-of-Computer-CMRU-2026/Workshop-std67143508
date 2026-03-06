<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        /* Animated Background Blobs */
        .blob {
            position: absolute;
            width: 600px;
            height: 600px;
            background: rgba(255, 255, 255, 0.1);
            filter: blur(100px);
            border-radius: 50%;
            z-index: -1;
            animation: move 25s infinite alternate;
        }
        .blob-1 { top: -150px; left: -150px; background: rgba(255, 255, 255, 0.15); }
        .blob-2 { bottom: -150px; right: -150px; background: rgba(37, 99, 235, 0.4); animation-delay: -7s; }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(150px, 150px) scale(1.3); }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border-radius: 45px;
            padding: 50px;
            width: 100%;
            max-width: 480px;
            text-align: center;
            color: white;
            position: relative;
        }

        .logo-container {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        h1 {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 5px;
            letter-spacing: -1.5px;
            line-height: 1.1;
        }

        .subtitle {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 30px;
        }

        .description {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
            margin-bottom: 35px;
        }

        .input-wrapper {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 5px 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .input-wrapper:focus-within {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            transform: scale(1.02);
        }

        .input-wrapper input {
            background: transparent;
            border: none;
            outline: none;
            color: white;
            padding: 15px 10px;
            width: 100%;
            font-size: 1rem;
            font-weight: 500;
        }

        .input-wrapper input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .btn-login {
            background: #ffffff;
            color: #1d4ed8;
            padding: 18px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 1.1rem;
            width: 100%;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            background: rgba(255, 255, 255, 0.95);
        }

        .footer-links {
            margin-top: 25px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.3);
            padding-bottom: 2px;
        }

        .footer-links a:hover {
            border-color: white;
        }

        .learning-tag {
            margin-top: 35px;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 700;
        }

        /* Error Styles */
        .error-msg {
            color: #fee2e2;
            font-size: 0.8rem;
            margin-bottom: 10px;
            text-align: left;
            padding-left: 10px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="glass-card">
        <div class="logo-container">
            <svg width="50" height="40" viewBox="0 0 100 80" fill="white" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="22" r="13"/>
                <path d="M26 72c0-13.3 10.7-24 24-24s24 10.7 24 24H26z"/>
                <circle cx="22" cy="30" r="10"/>
                <path d="M2 72h22v-2c0-6.2 2.7-11.9 7-15.9C28.4 52.9 25.3 52 22 52c-11 0-20 9-20 20z"/>
                <circle cx="78" cy="30" r="10"/>
                <path d="M98 72H76v-2c0-6.2-2.7-11.9 7-15.9C85.6 52.9 88.7 52 92 52c11 0 20 9 6 20z"/>
            </svg>
        </div>

        <h1>Senior-to-Junior</h1>
        <div class="subtitle">Workshop System</div>

        <p class="description">
            ระบบลงทะเบียนกิจกรรมเวิร์กชอป พี่สอนน้อง <br> 
            พัฒนาทักษะและความร่วมมือระหว่างชั้นปี
        </p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-wrapper">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="EMAIL" required autofocus autocomplete="username">
            </div>
            @error('email') <div class="error-msg">{{ $message }}</div> @enderror

            <div class="input-wrapper">
                <input type="password" name="password" placeholder="PASSWORD" required autocomplete="current-password">
            </div>
            @error('password') <div class="error-msg">{{ $message }}</div> @enderror

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; font-size: 0.85rem; padding: 0 10px;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="remember" style="margin-right: 8px; accent-color: white;">
                    Remember me
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="color: white; text-decoration: none; opacity: 0.8;">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn-login">
                ลงชื่อเข้าใช้งาน
            </button>
        </form>

        <div class="footer-links">
            Don't have an account? <a href="{{ route('register') }}">Sign up</a>
        </div>

        <div class="learning-tag">
            LEARNING TOGETHER
        </div>
    </div>
</body>
</html>
