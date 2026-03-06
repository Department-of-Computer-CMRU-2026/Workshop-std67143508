<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Workshop System') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #93c5fd 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            padding: 20px;
            box-sizing: border-box;
        }

        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
            animation: move 20s infinite alternate;
        }
        .blob-1 { top: -100px; left: -100px; background: rgba(59, 130, 246, 0.3); }
        .blob-2 { bottom: -100px; right: -100px; background: rgba(147, 197, 253, 0.3); animation-delay: -5s; }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(100px, 100px) scale(1.2); }
        }

        /* ---- GUEST CARD (same as before) ---- */
        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            border-radius: 40px;
            padding: 60px 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
            color: white;
            position: relative;
        }

        .logo-container {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 16px 32px;
            border-radius: 20px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 15px;
        }
        .btn-glass:hover { background: rgba(255,255,255,0.3); transform: translateY(-2px); }
        .btn-primary-glass { background: #ffffff; color: #1a4fd6; border: none; }
        .btn-primary-glass:hover { background: rgba(255,255,255,0.9); color: #1e3a8a; }

        h1 { font-size: 2.5rem; font-weight: 800; margin-bottom: 5px; letter-spacing: -1px; line-height: 1.2; }
        .subtitle { font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 3px; color: rgba(255,255,255,0.8); margin-bottom: 25px; }
        .description { font-size: 1rem; color: rgba(255,255,255,0.7); line-height: 1.6; margin-bottom: 40px; }

        /* ---- AUTH USER CARD (full-width wider) ---- */
        .auth-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 20px 60px 0 rgba(0, 0, 0, 0.3);
            border-radius: 40px;
            padding: 40px 50px;
            width: 100%;
            max-width: 820px;
            color: white;
            position: relative;
        }

        .auth-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 35px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .auth-header-left { display: flex; align-items: center; gap: 18px; }

        .mini-logo {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .auth-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .auth-header p { margin: 0; font-size: 0.85rem; color: rgba(255,255,255,0.65); font-weight: 600; }

        .header-actions { display: flex; gap: 12px; flex-shrink: 0; }

        .btn-dash {
            background: #ffffff;
            color: #1a4fd6;
            padding: 12px 24px;
            border-radius: 16px;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-dash:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); background: rgba(255,255,255,0.9); }

        .section-title {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: rgba(255,255,255,0.5);
            margin-bottom: 16px;
        }

        .reg-list { display: flex; flex-direction: column; gap: 12px; }

        .reg-item {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.3s ease;
        }
        .reg-item:hover { background: rgba(255,255,255,0.2); transform: translateX(4px); }

        .reg-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .reg-info { flex: 1; min-width: 0; }
        .reg-info .title { font-weight: 800; font-size: 1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .reg-info .meta { font-size: 0.78rem; color: rgba(255,255,255,0.6); margin-top: 2px; font-weight: 600; }

        .reg-badge {
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: rgba(255,255,255,0.2);
            flex-shrink: 0;
        }

        .prog-bar-wrap { margin-top: 30px; }
        .prog-label { display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.6); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
        .prog-track { height: 8px; background: rgba(255,255,255,0.15); border-radius: 20px; overflow: hidden; }
        .prog-fill { height: 100%; background: #ffffff; border-radius: 20px; transition: width 0.7s ease; }

        .empty-state { text-align: center; padding: 40px 20px; }
        .empty-state p { color: rgba(255,255,255,0.55); font-weight: 600; font-size: 0.95rem; margin-top: 12px; }

        .footer-tag { margin-top: 28px; text-align: center; font-size: 0.65rem; color: rgba(255,255,255,0.35); text-transform: uppercase; letter-spacing: 3px; }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    @auth
        {{-- ===== LOGGED IN: Show My Registrations ===== --}}
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-header-left">
                    <div class="mini-logo">
                        <svg width="32" height="26" viewBox="0 0 100 80" fill="white"><circle cx="50" cy="22" r="13"/><path d="M26 72c0-13.3 10.7-24 24-24s24 10.7 24 24H26z"/><circle cx="22" cy="30" r="10"/><path d="M2 72h22v-2c0-6.2 2.7-11.9 7-15.9C28.4 52.9 25.3 52 22 52c-11 0-20 9-20 20z"/><circle cx="78" cy="30" r="10"/><path d="M98 72H76v-2c0-6.2-2.7-11.9 7-15.9C85.6 52.9 88.7 52 92 52c11 0 20 9 6 20z"/></svg>
                    </div>
                    <div>
                        <h1>Welcome, {{ auth()->user()->name }}</h1>
                        <p>Senior-to-Junior · Workshop System</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('dashboard') }}" class="btn-dash">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                </div>
            </div>

            {{-- Registration Progress Bar --}}
            @php $count = count($registeredWorkshops); @endphp
            <div class="prog-bar-wrap" style="margin-bottom: 28px;">
                <div class="prog-label">
                    <span>My Registrations</span>
                    <span>{{ $count }} / 3 Activities</span>
                </div>
                <div class="prog-track">
                    <div class="prog-fill" style="width: {{ ($count / 3) * 100 }}%"></div>
                </div>
            </div>

            <div class="section-title">Registered Activities</div>

            @if($count > 0)
                <div class="reg-list">
                    @foreach($registeredWorkshops as $i => $workshop)
                        <div class="reg-item">
                            <div class="reg-icon">
                                <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <div class="reg-info">
                                <div class="title">{{ $workshop->title }}</div>
                                <div class="meta">🎤 {{ $workshop->speaker }} &nbsp;·&nbsp; 📍 {{ $workshop->location }}</div>
                            </div>
                            <div class="reg-badge">#{{ sprintf('%02d', $i + 1) }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <svg width="48" height="48" fill="none" stroke="rgba(255,255,255,0.4)" viewBox="0 0 24 24" style="margin: 0 auto;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p>ยังไม่ได้ลงทะเบียนกิจกรรมใด<br>ไปที่ Dashboard เพื่อดูกิจกรรมที่เปิดให้ลงทะเบียน!</p>
                    <a href="{{ route('dashboard') }}" class="btn-dash" style="display: inline-flex; margin-top: 20px; padding: 14px 32px;">
                        ไปที่ Dashboard
                    </a>
                </div>
            @endif

            <div class="footer-tag">Learning Together</div>
        </div>

    @else
        {{-- ===== GUEST: Show Login / Register ===== --}}
        <div class="glass-card">
            <div class="logo-container">
                <svg width="60" height="48" viewBox="0 0 100 80" fill="white" xmlns="http://www.w3.org/2000/svg">
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

            <div class="actions">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-glass btn-primary-glass">ลงชื่อเข้าใช้งาน</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-glass">สมัครสมาชิกใหม่</a>
                    @endif
                @endif
            </div>

            <div style="margin-top: 20px; font-size: 0.75rem; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 2px;">
                Learning Together
            </div>
        </div>
    @endauth
</body>
</html>
