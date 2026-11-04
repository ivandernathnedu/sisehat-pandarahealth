<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiSehat - Tautan Tidak Valid</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-color: #030303;
            --card-bg: rgba(20, 20, 20, 0.6);
            --border-color: rgba(255, 255, 255, 0.06);
            --text-primary: #ffffff;
            --text-secondary: #8E8E93;
            --accent-red: #ff453a;
            --accent-glow: rgba(255, 69, 58, 0.15);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow: hidden;
            position: relative;
        }

        /* Background ambient glow effect */
        .ambient-glow {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--accent-glow) 0%, rgba(3, 3, 3, 0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            pointer-events: none;
            filter: blur(40px);
        }

        .error-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 28px;
            padding: 48px 32px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.8), 0 0 80px rgba(255, 69, 58, 0.03);
            z-index: 2;
            position: relative;
            animation: cardAppear 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.96);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .icon-wrapper {
            position: relative;
            width: 88px;
            height: 88px;
            margin: 0 auto 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-bg {
            position: absolute;
            inset: 0;
            background: rgba(255, 69, 58, 0.08);
            border-radius: 50%;
            border: 1px solid rgba(255, 69, 58, 0.2);
            animation: pulseGlow 2.5s infinite ease-in-out;
        }

        @keyframes pulseGlow {
            0%, 100% {
                transform: scale(1);
                opacity: 0.8;
            }
            50% {
                transform: scale(1.15);
                opacity: 0.4;
            }
        }

        .error-icon {
            font-size: 38px;
            color: var(--accent-red);
            z-index: 2;
            filter: drop-shadow(0 0 10px rgba(255, 69, 58, 0.5));
        }

        .error-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 14px;
            color: var(--text-primary);
        }

        .error-desc {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 32px;
            padding: 0 12px;
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.06) 50%, rgba(255,255,255,0) 100%);
            margin-bottom: 28px;
        }

        .system-brand {
            font-size: 12px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.2);
            letter-spacing: 1px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="ambient-glow"></div>

    <div class="error-card">
        <div class="icon-wrapper">
            <div class="icon-bg"></div>
            <i class="fa-solid fa-link-slash error-icon"></i>
        </div>
        
        <h1 class="error-title">Tautan Tidak Dapat Diakses</h1>
        <p class="error-desc">{{ $message ?? 'Tautan ini mungkin sudah kadaluarsa atau tidak valid.' }}</p>
        
        <div class="divider"></div>
        
        <div class="system-brand">
            Pandara <span style="font-weight: 400;">Health</span>
        </div>
    </div>

</body>
</html>
