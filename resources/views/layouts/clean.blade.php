<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiSehat - @yield('title', 'Pendataan Organisasi')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        .topbar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            height: 60px;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-color);
            flex-shrink: 0;
        }
        .main-content {
            flex: 1;
            padding: 40px;
            max-width: 860px;
            width: 100%;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .topbar-header {
                padding: 0 20px;
            }
            .main-content {
                padding: 24px 16px;
            }
        }
        @media (max-width: 480px) {
            .topbar-header {
                padding: 0 16px;
            }
            .main-content {
                padding: 16px 12px;
            }
        }
    </style>

    @yield('styles')
</head>
<body style="background: var(--bg-color); min-height: 100vh; display: flex; flex-direction: column;">

    <!-- Topbar -->
    <header class="topbar-header">
        <div style="font-size: 15px; font-weight: 700;">
            Pandara <span style="font-weight: 400; color: var(--text-secondary);">Health</span>
        </div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <i class="fa-regular fa-bell" style="color: var(--text-secondary); font-size: 16px; cursor: pointer;"></i>
            <i class="fa-regular fa-circle-question" style="color: var(--text-secondary); font-size: 16px; cursor: pointer;"></i>
            <div style="
                width: 36px; height: 36px;
                border: 1px solid var(--border-color);
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                cursor: pointer;
            ">
                <i class="fa-solid fa-user" style="color: var(--text-secondary); font-size: 14px;"></i>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="main-content">
        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
