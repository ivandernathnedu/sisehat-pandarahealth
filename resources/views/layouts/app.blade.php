<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiSehat - @yield('title', 'Ecosystem Overview')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .blur-content {
            filter: blur(8px);
            pointer-events: none;
            user-select: none;
        }
        .popup-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }
        .popup-box {
            background: var(--card-color, #151515);
            border: 1px solid var(--border-color, #333);
            padding: 32px;
            border-radius: 16px;
            text-align: center;
            max-width: 360px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .popup-box h3 {
            font-size: 18px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .popup-box p {
            color: var(--text-secondary, #aaa);
            margin-bottom: 24px;
            font-size: 13px;
            line-height: 1.5;
        }
        .popup-box .btn-sm-solid {
            text-decoration: none;
            display: inline-block;
            padding: 10px 20px;
            font-size: 13px;
            background: #fff;
            color: #000;
            border-radius: 8px;
            font-weight: 600;
        }

        /* Sidebar Footer Account Section */
        .sidebar-account {
            display: flex; align-items: center; gap: 12px;
            padding: 12px; border-radius: 12px;
            background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);
            text-decoration: none; color: inherit;
            margin-bottom: 12px; transition: all 0.2s;
        }
        .sidebar-account:hover { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.1); }
        .sidebar-account-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: #2a2a2a; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; flex-shrink: 0;
            overflow: hidden;
        }
        .sidebar-account-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .sidebar-account-info { min-width: 0; }
        .sidebar-account-name { 
            font-size: 13px; font-weight: 600; color: #fff; 
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; 
        }
        .sidebar-account-email { 
            font-size: 11px; color: #666; 
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; 
        }

        /* Guarantee mobile header and menu toggle are hidden on desktop viewports */
        @media (min-width: 769px) {
            .mobile-header {
                display: none !important;
            }
            .menu-toggle {
                display: none !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="appSidebar">
            <div class="logo" style="display:flex; align-items:center; gap:14px; margin-bottom:48px;">
                <img src="{{ asset('images/logo_pandara.png') }}" alt="Pandara" style="width:44px; height:44px; object-fit:contain; flex-shrink:0;">
                <h2 style="font-size: 17px; font-weight: 700; line-height:1.2;">Pandara <span style="font-weight: 400; color: var(--text-secondary);">Health</span></h2>
            </div>
            
            <nav class="nav-menu">
                <div class="nav-item">
                    <a href="{{ route('beranda') }}" class="nav-link {{ request()->routeIs('beranda') ? 'active' : '' }}">
                        <i class="fa-solid fa-house"></i>
                        <span>Beranda</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('umkm-rank') }}" class="nav-link {{ request()->routeIs('umkm-rank') ? 'active' : '' }}">
                        <i class="fa-solid fa-ranking-star"></i>
                        <span>Full Rank UMKM</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('assessment') }}" class="nav-link {{ request()->routeIs('assessment') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-pen"></i>
                        <span>Isi Assessment</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('profil-faktor') }}" class="nav-link {{ request()->routeIs('profil-faktor') ? 'active' : '' }}">
                        <i class="fa-solid fa-users-gear"></i>
                        <span>Profil 6 Faktor</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('comparison') }}" class="nav-link {{ request()->routeIs('comparison') ? 'active' : '' }}">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Perbandingan Faktor</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('rekomendasi') }}" class="nav-link {{ request()->routeIs('rekomendasi') ? 'active' : '' }}">
                        <i class="fa-solid fa-lightbulb"></i>
                        <span>Rekomendasi</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('monitoring') }}" class="nav-link {{ request()->routeIs('monitoring') ? 'active' : '' }}">
                        <i class="fa-solid fa-tower-broadcast"></i>
                        <span>Monitoring Respon</span>
                    </a>
                </div>
            </nav>
            
            <div class="sidebar-footer" style="margin-top: auto;">
                @if(Auth::guard('owner')->check())
                <a href="{{ route('profile') }}" class="sidebar-account">
                    <div class="sidebar-account-avatar">
                        @if(Auth::guard('owner')->user()->photo)
                            <img src="{{ asset('storage/' . Auth::guard('owner')->user()->photo) }}" alt="{{ Auth::guard('owner')->user()->name }}">
                        @else
                            {{ strtoupper(substr(Auth::guard('owner')->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="sidebar-account-info">
                        <div class="sidebar-account-name">{{ Auth::guard('owner')->user()->name }}</div>
                        <div class="sidebar-account-email">{{ Auth::guard('owner')->user()->email }}</div>
                    </div>
                </a>
                @endif
                
                <form id="logout-form" action="{{ route('logout.post') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                
                <button class="btn btn-outline" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content" style="position: relative;">
            <!-- Mobile Header -->
            <div class="mobile-header">
                <div style="display:flex; align-items:center; gap:12px;">
                    <img src="{{ asset('images/logo_pandara.png') }}" alt="Pandara" style="width:32px; height:32px;">
                    <span style="font-weight:700; font-size:16px;">Pandara <span style="font-weight:400; color:var(--text-secondary);">Health</span></span>
                </div>
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>

            <section class="content {{ ((isset($show_umkm_popup) && $show_umkm_popup) || (isset($show_assessment_popup) && $show_assessment_popup)) && !Route::is('dashboard') ? 'blur-content' : '' }}">
                @if(isset($show_in_page_notice) && $show_in_page_notice && !Route::is('dashboard'))
                    <div style="background: rgba(250, 204, 21, 0.1); border: 1px solid rgba(250, 204, 21, 0.3); border-radius: 12px; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="width: 40px; height: 40px; background: rgba(250, 204, 21, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #facc15;">
                                <i class="fa-solid fa-clipboard-list" style="font-size: 20px;"></i>
                            </div>
                            <div>
                                <div style="font-size: 14px; font-weight: 600; color: #fff;">Anda belum melakukan assessment</div>
                                <div style="font-size: 12px; color: var(--text-secondary);">UMKM ini belum memiliki data evaluasi. Silakan buat asesmen untuk melihat hasil analisis.</div>
                            </div>
                        </div>
                        <a href="{{ route('assessment') }}" class="btn-sm-solid" style="background: #facc15; color: #000; text-decoration: none; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                            Buat Assessment
                        </a>
                    </div>
                @endif
                @yield('content')
            </section>
            
            @if(!Route::is('dashboard'))
                @if(isset($show_umkm_popup) && $show_umkm_popup)
                <div class="popup-overlay">
                    <div class="popup-box">
                        <i class="fa-solid fa-store-slash" style="font-size:40px; color:#666; margin-bottom:16px;"></i>
                        <h3>UMKM Belum Terdaftar</h3>
                        <p>Anda belum mendaftarkan UMKM. Silakan daftarkan UMKM Anda untuk melihat analisis dan rekomendasi.</p>
                        <a href="{{ route('tambah-umkm') }}" class="btn-sm-solid">Segera Daftarkan UMKM Anda</a>
                    </div>
                </div>
                @elseif(isset($show_assessment_popup) && $show_assessment_popup)
                <div class="popup-overlay">
                    <div class="popup-box">
                        <i class="fa-solid fa-clipboard-list" style="font-size:40px; color:#666; margin-bottom:16px;"></i>
                        <h3>Asesmen Belum Dibuat</h3>
                        <p>Harap buat asesmen terlebih dahulu untuk mulai mengumpulkan data dan melihat hasil analisis.</p>
                        <a href="{{ route('assessment') }}" class="btn-sm-solid">Buat Asesmen Sekarang</a>
                    </div>
                </div>
                @endif
            @endif
        </main>
    </div>

    <!-- Toast Notification System -->
    <div id="toast-container" style="position: fixed; bottom: 24px; right: 24px; display: flex; flex-direction: column; gap: 12px; z-index: 9999;"></div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            // Basic styling
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
            
            // Animate in
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            }, 10);
            
            // Animate out and remove
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Catch Laravel Session Flash Messages
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        // Mobile Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }
    </script>

    @yield('scripts')
</body>
</html>
