<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pandara Health — @yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body {
            background: #0a0a0a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        /* Background gradient spot */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse 80% 60% at 50% 40%, rgba(40,40,40,0.6) 0%, transparent 70%);
            pointer-events: none;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 460px;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
        }

        /* Brand logo on top */
        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 28px;
        }
        .brand-icon { font-size: 18px; color: #fff; }
        .brand-name { font-size: 17px; font-weight: 600; letter-spacing: -0.2px; }

        /* Auth Card */
        .auth-card {
            background: #161616;
            border: 1px solid #262626;
            border-radius: 16px;
            padding: 36px 32px;
        }

        .auth-title { font-size: 28px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .auth-subtitle { font-size: 13px; color: #737373; text-align: center; line-height: 1.6; margin-bottom: 32px; }

        /* Form fields */
        .field { margin-bottom: 18px; }
        .field-label {
            display: block; font-size: 10px; font-weight: 700;
            letter-spacing: 1px; color: #737373; text-transform: uppercase;
            margin-bottom: 8px;
        }
        .field-inner {
            position: relative; display: flex; align-items: center;
            background: #1e1e1e; border: 1px solid #2e2e2e;
            border-radius: 10px; overflow: hidden;
            transition: border-color 0.2s;
        }
        .field-inner:focus-within { border-color: #555; }
        .field-icon {
            padding: 0 14px; color: #555; font-size: 14px;
            display: flex; align-items: center; flex-shrink: 0;
        }
        .field-input {
            flex: 1; background: transparent; border: none; outline: none;
            color: #fff; font-size: 13px; padding: 13px 14px 13px 0;
            font-family: 'Inter', sans-serif;
        }
        .field-input::placeholder { color: #444; }
        .field-input[type="password"] { letter-spacing: 2px; }
        .field-input[type="password"]::placeholder { letter-spacing: 0; }
        .field-right { padding: 0 14px; color: #555; font-size: 13px; cursor: pointer; }

        .field-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .field-row .field-label { margin-bottom: 0; }
        .forgot-link { font-size: 12px; color: #737373; text-decoration: none; }
        .forgot-link:hover { color: #fff; }

        /* Submit button */
        .btn-submit {
            width: 100%; padding: 14px;
            background: #fff; color: #000;
            border: none; border-radius: 10px;
            font-size: 13px; font-weight: 700;
            letter-spacing: 0.5px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            gap: 10px; margin-top: 8px; transition: background 0.2s, transform 0.15s;
        }
        .btn-submit:hover { background: #e5e5e5; transform: translateY(-1px); }

        /* Footer link */
        .auth-footer { text-align: center; margin-top: 24px; font-size: 13px; color: #737373; }
        .auth-footer a { color: #fff; text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }

        /* Page footer */
        .page-footer {
            margin-top: 28px; font-size: 11px; color: #3a3a3a;
            text-align: center; position: relative; z-index: 1;
        }

        @media (max-width: 480px) {
            .auth-card { padding: 32px 20px; }
            .auth-title { font-size: 24px; }
            .field-row { flex-direction: column; align-items: flex-start; gap: 8px; }
        }
    </style>
</head>
<body>

    <div class="auth-wrapper">
        <!-- Brand -->
        <div class="brand" style="display:flex; align-items:center; gap:12px; justify-content:center; margin-bottom:28px;">
            <img src="/images/logo_pandara.png" alt="Pandara" style="width:40px; height:40px; object-fit:contain;">
            <span class="brand-name" style="font-size:18px;">Pandara Health</span>
        </div>

        <!-- Card -->
        <div class="auth-card">
            @yield('content')
        </div>
    </div>

    <p class="page-footer">© 2026 Pandara Health System. Secure Analytics.</p>

    <!-- Toast Notification System -->
    <div id="toast-container" style="position: fixed; bottom: 24px; right: 24px; display: flex; flex-direction: column; gap: 12px; z-index: 9999;"></div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            toast.style.minWidth = '250px';
            toast.style.background = '#1a1a1a';
            toast.style.border = '1px solid #333';
            toast.style.borderLeft = `4px solid ${type === 'success' ? '#4ade80' : '#f87171'}`;
            toast.style.color = '#fff';
            toast.style.padding = '16px';
            toast.style.borderRadius = '8px';
            toast.style.boxShadow = '0 10px 30px rgba(0,0,0,0.5)';
            toast.style.display = 'flex';
            toast.style.alignItems = 'center';
            toast.style.gap = '12px';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'all 0.3s ease';
            
            const icon = type === 'success' ? '<i class="fa-solid fa-circle-check" style="color:#4ade80;"></i>' : '<i class="fa-solid fa-circle-exclamation" style="color:#f87171;"></i>';
            
            toast.innerHTML = `
                ${icon}
                <div style="font-size: 13px; font-weight: 500;">${message}</div>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            }, 10);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    </script>

    @yield('scripts')
</body>
</html>
