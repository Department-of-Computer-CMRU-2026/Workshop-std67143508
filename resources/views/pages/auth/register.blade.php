<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Register</title>
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
            padding: 40px 50px;
            width: 100%;
            max-width: 480px;
            text-align: center;
            color: white;
            position: relative;
        }

        .logo-container {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        h1 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 5px;
            letter-spacing: -1px;
            line-height: 1.1;
        }

        .subtitle {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 25px;
        }

        .input-wrapper {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 18px;
            padding: 2px 18px;
            margin-bottom: 12px;
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
            padding: 12px 0;
            width: 100%;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .input-wrapper input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .btn-register {
            background: #ffffff;
            color: #1d4ed8;
            padding: 16px;
            border-radius: 18px;
            font-weight: 800;
            font-size: 1.1rem;
            width: 100%;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .btn-register:hover {
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

        .error-msg {
            color: #fee2e2;
            font-size: 0.75rem;
            margin-bottom: 8px;
            text-align: left;
            padding-left: 10px;
            font-weight: 600;
        }

        .learning-tag {
            margin-top: 30px;
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="glass-card">
        <div class="logo-container">
            <svg width="40" height="32" viewBox="0 0 100 80" fill="white" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="22" r="13"/>
                <path d="M26 72c0-13.3 10.7-24 24-24s24 10.7 24 24H26z"/>
                <circle cx="22" cy="30" r="10"/>
                <path d="M2 72h22v-2c0-6.2 2.7-11.9 7-15.9C28.4 52.9 25.3 52 22 52c-11 0-20 9-20 20z"/>
                <circle cx="78" cy="30" r="10"/>
                <path d="M98 72H76v-2c0-6.2-2.7-11.9 7-15.9C85.6 52.9 88.7 52 92 52c11 0 20 9 6 20z"/>
            </svg>
        </div>

        <h1>Create Account</h1>
        <div class="subtitle">Join the Workshop</div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="input-wrapper">
                <input type="text" name="name" value="{{ old('name') }}" placeholder="FULL NAME" required autofocus autocomplete="name">
            </div>
            @error('name') <div class="error-msg">{{ $message }}</div> @enderror

            <div class="input-wrapper">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="EMAIL ADDRESS" required autocomplete="username">
            </div>
            @error('email') <div class="error-msg">{{ $message }}</div> @enderror

            <div class="input-wrapper">
                <input type="password" name="password" placeholder="PASSWORD" required autocomplete="new-password">
            </div>
            @error('password') <div class="error-msg">{{ $message }}</div> @enderror

            <div class="input-wrapper">
                <input type="password" name="password_confirmation" placeholder="CONFIRM PASSWORD" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn-register">
                ลงทะเบียน
            </button>
        </form>

        <div class="footer-links">
            Already have an account? <a href="{{ route('login') }}">Login</a>
        </div>

        <div class="learning-tag">
            LEARNING TOGETHER
        </div>
    </div>
</body>
</html>
