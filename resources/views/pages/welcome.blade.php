<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pandara Health | Platform Analisis Kesehatan Organisasi</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-color: #050505;
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.6);
            --accent: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .hero-wrapper {
            background-image: url("{{ asset('images/bg_welcome_screen_bw.jpeg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            z-index: 1;
        }

        .hero-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.45);
            z-index: -1;
            pointer-events: none;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 4rem;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .hero-container {
            min-height: 100vh;
        }

        /* Navbar */
        nav {
            padding: 2.5rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            letter-spacing: 1px;
            font-size: 1.1rem;
            text-transform: uppercase;
        }

        .logo img {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        /* Hero Section */
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 2rem 0 4rem 0;
            flex-grow: 1;
            gap: 4rem;
        }

        .hero-content {
            flex: 1.2;
            max-width: 650px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 100px;
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 2rem;
            color: rgba(255,255,255,0.8);
        }

        .badge::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 0 10px #fff;
        }

        h1 {
            font-size: 3.8rem;
            font-weight: 700;
            line-height: 1.05;
            margin-bottom: 2rem;
            letter-spacing: -2px;
        }

        .subtext {
            font-size: 1.15rem;
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 3rem;
            max-width: 550px;
        }

        .cta-group {
            display: flex;
            gap: 1.2rem;
        }

        .btn {
            padding: 1.1rem 2.8rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: #fff;
            color: #000;
            border: 1px solid #fff;
        }

        .btn-primary:hover {
            background: transparent;
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255,255,255,0.1);
        }

        .btn-outline {
            background: rgba(255,255,255,0.03);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }

        /* Visual Stack (Right Side) - 2x2 Grid Staggered Layout */
        .visual-stack {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            align-items: start;
        }

        /* Staggered column effect */
        .visual-stack > .glass-card:nth-child(even) {
            transform: translateY(3rem);
        }

        .glass-card {
            padding: 1.8rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.01));
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-top: 1px solid rgba(255, 255, 255, 0.25);
            border-left: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(255, 255, 255, 0.03);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .visual-stack > .glass-card:nth-child(odd):hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.03));
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-10px);
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.6);
        }

        .visual-stack > .glass-card:nth-child(even):hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.03));
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(calc(3rem - 10px));
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.6);
        }

        .card-icon {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.2rem;
        }

        .card-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.6rem;
        }

        .card-desc {
            font-size: 0.85rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* Trusted By */
        .trusted-by {
            padding: 6rem 0 3rem 0;
            text-align: center;
        }

        .trusted-text {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.3);
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2.5rem;
            padding-bottom: 8rem;
        }

        .feature-item {
            padding: 3rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.01));
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.4s ease;
        }

        .feature-item:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02));
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .feature-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 1.2rem;
            letter-spacing: -0.5px;
        }

        .feature-desc {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.7;
        }

        /* Footer */
        footer {
            padding: 3rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.4);
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-brand {
            font-weight: 700;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 2px;
            font-size: 0.9rem;
        }

        @media (max-width: 1200px) {
            .container { padding: 0 2rem; }
            h1 { font-size: 3rem; }
        }

        @media (max-width: 1024px) {
            .hero {
                flex-direction: column;
                text-align: center;
                padding-top: 0;
                gap: 4rem;
            }
            .hero-content {
                max-width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .subtext {
                margin-left: auto;
                margin-right: auto;
            }
            .cta-group {
                justify-content: center;
            }
            .visual-stack {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .container { padding: 0 1.5rem; }
            h1 { font-size: 2.5rem; }
            .hero { gap: 3rem; }
            .visual-stack {
                grid-template-columns: 1fr;
            }
            .visual-stack > .glass-card:nth-child(even) {
                transform: none;
            }
            .visual-stack > .glass-card:nth-child(even):hover {
                transform: translateY(-10px);
            }
            .footer-content { flex-direction: column; gap: 1rem; text-align: center; }
        }

        @media (max-width: 480px) {
            h1 { font-size: 2rem; letter-spacing: -1px; }
            .subtext { font-size: 1rem; }
            .cta-group { flex-direction: column; width: 100%; gap: 1rem; }
            .btn { width: 100%; }
            .feature-item { padding: 2rem; }
        }
    </style>
</head>
<body>
    <div class="hero-wrapper">
        <div class="container hero-container">
            <nav>
                <div class="logo">
                    <img src="{{ asset('images/logo_pandara.png') }}" alt="Pandara Health Logo">
                    Pandara Health
                </div>
                <!-- Navbar links removed as requested -->
            </nav>

            <section class="hero">
                <div class="hero-content">
                    <div class="badge">Enterprise Health Intelligence</div>
                    <h1>Platform Analisis Kesehatan Organisasi UMKM Berbasis Data.</h1>
                    <p class="subtext">Ukur dan optimalkan kesehatan struktural dan kultural bisnis Anda secara profesional melalui kecerdasan buatan dan metrik data presisi.</p>
                    <div class="cta-group">
                        <a href="{{ route('login') }}" class="btn btn-primary">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-outline">Daftar Akun</a>
                    </div>
                </div>

                <div class="visual-stack">
                    <div class="glass-card card-1">
                        <div class="card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                        </div>
                        <div class="card-title">Real-time Data</div>
                        <div class="card-desc">Monitoring kesehatan struktur 24/7.</div>
                    </div>
                    
                    <div class="glass-card card-2">
                        <div class="card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                        <div class="card-title">Cultur Audit</div>
                        <div class="card-desc">Analisis mendalam budaya kerja organisasi.</div>
                    </div>

                    <div class="glass-card card-3">
                        <div class="card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        </div>
                        <div class="card-title">AI Insights</div>
                        <div class="card-desc">Prediksi tantangan bisnis masa depan.</div>
                    </div>

                    <div class="glass-card card-4">
                        <div class="card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </div>
                        <div class="card-title">Secure Core</div>
                        <div class="card-desc">Enkripsi data standar global.</div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="container">
        <div class="trusted-by">
            <div class="trusted-text">Trusted by global scale-up enterprises</div>
        </div>

        <section class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                </div>
                <div class="feature-title">Metrik Presisi</div>
                <div class="feature-desc">Sistem evaluasi berbasis skor numerik untuk setiap aspek organisasi Anda, dari efisiensi hingga loyalitas.</div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                </div>
                <div class="feature-title">Interkonektivitas</div>
                <div class="feature-desc">Hubungkan data antar departemen untuk melihat gambaran besar kesehatan ekosistem bisnis Anda.</div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 11 11 13 15 9"></polyline></svg>
                </div>
                <div class="feature-title">Sertifikasi</div>
                <div class="feature-desc">Dapatkan sertifikasi kesehatan organisasi yang diakui secara profesional untuk menarik investor.</div>
            </div>
        </section>

        <footer>
            <div class="footer-content">
                <div class="footer-brand">PANDARA HEALTH</div>
                <div class="footer-legal">© 2026 PANDARA HEALTH SYSTEM | PROPRIETARY & CONFIDENTIAL</div>
            </div>
        </footer>
    </div>
</body>
</html>
