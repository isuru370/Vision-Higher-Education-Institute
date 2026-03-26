<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VISION ACADEMY OF HIGHER EDUCATION · welcome</title>

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;1,14..32,300&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-dark: #0f172a;
            --bg-mid: #1e293b;
            --bg-light: #334155;
            --red-main: #dc2626;
            --red-deep: #991b1b;
            --orange-glow: #f97316;
            --text-main: #f8fafc;
            --text-soft: #cbd5e1;
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.12);
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            color: var(--text-main);
            background:
                radial-gradient(circle at top left, rgba(220, 38, 38, 0.18), transparent 30%),
                radial-gradient(circle at bottom right, rgba(249, 115, 22, 0.14), transparent 30%),
                linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-mid) 55%, #111827 100%);
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
            pointer-events: none;
        }

        .floating-shapes {
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.35;
            animation: float 10s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 240px;
            height: 240px;
            top: 8%;
            left: -4%;
            background: rgba(220, 38, 38, 0.35);
        }

        .shape:nth-child(2) {
            width: 280px;
            height: 280px;
            bottom: 2%;
            right: -5%;
            background: rgba(249, 115, 22, 0.22);
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 140px;
            height: 140px;
            top: 65%;
            left: 8%;
            background: rgba(255, 255, 255, 0.08);
            animation-delay: 4s;
        }

        .shape:nth-child(4) {
            width: 190px;
            height: 190px;
            top: 18%;
            right: 10%;
            background: rgba(220, 38, 38, 0.18);
            animation-delay: 1s;
        }

        .shape:nth-child(5) {
            width: 100px;
            height: 100px;
            bottom: 18%;
            left: 18%;
            background: rgba(249, 115, 22, 0.16);
            animation-delay: 5s;
        }

        .container {
            text-align: center;
            z-index: 10;
            max-width: 700px;
            padding: 1.8rem 1.5rem;
            margin: 1.5rem;
    border-radius: 32px;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08), transparent 35%, transparent 65%, rgba(255, 255, 255, 0.03));
            pointer-events: none;
        }

        .logo-container {
            margin-bottom: 1.75rem;
            transition: transform 0.25s ease;
            position: relative;
            z-index: 2;
        }

        .logo-container:hover {
            transform: scale(1.02);
        }

        .logo {
            max-width: 240px;
            width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            filter:
                drop-shadow(0 0 18px rgba(220, 38, 38, 0.35)) drop-shadow(0 8px 20px rgba(0, 0, 0, 0.25));
        }

        h1 {
            font-size: 2.9rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
            letter-spacing: -0.03em;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
            color: var(--text-main);
            position: relative;
            z-index: 2;
        }

        .accent {
            background: linear-gradient(90deg, #ef4444 0%, #f97316 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            position: relative;
        }

        .accent::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -4px;
            width: 100%;
            height: 4px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(239, 68, 68, 0.7), rgba(249, 115, 22, 0.7));
            opacity: 0.55;
        }

        .tagline {
            font-size: 1.05rem;
            font-weight: 300;
            color: var(--text-soft);
            max-width: 650px;
            margin: 0 auto 2rem auto;
            line-height: 1.6;
            font-style: italic;
            padding: 1rem 1.25rem 1rem 1.5rem;
            border-left: 4px solid rgba(239, 68, 68, 0.85);
            background: rgba(255, 255, 255, 0.04);
            border-radius: 0 18px 18px 0;
            position: relative;
            z-index: 2;
        }

        .redirect-card {
            margin: 2.2rem auto 0;
            padding: 1.25rem 1.8rem;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.92), rgba(153, 27, 27, 0.95));
            display: inline-flex;
            align-items: center;
            gap: 1.2rem;
            color: white;
            box-shadow:
                0 14px 30px rgba(220, 38, 38, 0.28),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            z-index: 2;
        }

        .redirect-card:hover {
            transform: translateY(-2px);
            box-shadow:
                0 18px 36px rgba(220, 38, 38, 0.32),
                inset 0 1px 0 rgba(255, 255, 255, 0.18);
        }

        .redirect-card i {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.95);
        }

        .redirect-text {
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .countdown-badge {
            background: rgba(255, 255, 255, 0.96);
            color: var(--red-main);
            font-weight: 800;
            font-size: 1.5rem;
            padding: 0.25rem 1rem;
            border-radius: 999px;
            min-width: 70px;
            text-align: center;
            line-height: 1;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
        }

        .countdown-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.78);
            margin-top: 0.3rem;
        }

        .meta-note {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: var(--text-soft);
            z-index: 10;
            display: inline-block;
            padding: 0.65rem 1.2rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .meta-note i {
            color: #f87171;
            margin: 0 4px;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-24px) scale(1.05);
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 2rem 1.25rem;
                border-radius: 30px;
            }

            h1 {
                font-size: 2.1rem;
                flex-direction: column;
                gap: 0.2rem;
            }

            .tagline {
                font-size: 1.1rem;
                padding-left: 1rem;
            }

            .redirect-card {
                flex-direction: column;
                gap: 0.75rem;
                padding: 1.35rem 1.4rem;
                border-radius: 36px;
            }

            .countdown-badge {
                font-size: 1.8rem;
                min-width: 90px;
            }

            .accent::after {
                bottom: -2px;
            }
        }
    </style>
</head>

<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="container">
        <div class="logo-container">
            <img src="{{ asset('uploads/logo/logo.png') }}" alt="VISION ACADEMY OF HIGHER EDUCATION logo" class="logo"
                onerror="this.src='https://placehold.co/320x120/dc2626/ffffff?text=VISION+ACADEMY&font=inter';">
        </div>

        <h1>
            <span>Welcome to</span>
            <span class="accent">VISION ACADEMY OF HIGHER EDUCATION</span>
        </h1>

        <p class="tagline">
            <i class="fas fa-quote-left" style="font-size:0.9rem; opacity:0.65; margin-right:6px;"></i>
            Empowering minds, shaping futures through excellence in education
            <i class="fas fa-quote-right" style="font-size:0.9rem; opacity:0.65; margin-left:6px;"></i>
        </p>

        <div class="redirect-card">
            <i class="fas fa-arrow-right-to-bracket"></i>
            <span class="redirect-text">Redirecting to login</span>
            <div style="display: flex; flex-direction: column; align-items: center;">
                <span class="countdown-badge" id="countdown">5</span>
                <span class="countdown-label">seconds</span>
            </div>
        </div>

        <div class="meta-note">
            <i class="fas fa-graduation-cap"></i> secure portal ·
            <i class="fas fa-lock" style="font-size:0.8rem;"></i> encrypted
        </div>
    </div>

    <script>
        (function () {
            let secondsLeft = 5;
            const countdownSpan = document.getElementById('countdown');

            countdownSpan.textContent = secondsLeft;

            const timer = setInterval(() => {
                secondsLeft -= 1;
                countdownSpan.textContent = secondsLeft;

                if (secondsLeft <= 0) {
                    clearInterval(timer);
                    window.location.href = "{{ route('login') }}";
                }
            }, 1000);
        })();
    </script>
</body>

</html>