<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiSehat API - Full Documentation & Responses</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Fira+Code:wght@400;500&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #050505;
            --surface-color: #0d0d0d;
            --card-color: #121212;
            --border-color: #222222;
            --text-primary: #ffffff;
            --text-secondary: #a3a3a3;
            --primary: #ffffff;
            --accent: #6366f1;
            
            --get-color: #10b981;
            --post-color: #3b82f6;
            --put-color: #f59e0b;
            --delete-color: #ef4444;
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

        /* Header */
        header {
            border-bottom: 1px solid var(--border-color);
            background-color: rgba(5, 5, 5, 0.8);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fff;
        }
        .logo-icon {
            font-size: 24px;
            color: #fff;
        }
        .logo-text {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .dev-badge {
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 12px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }
        .back-btn {
            background: transparent;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 16px;
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .back-btn:hover {
            color: #fff;
            border-color: #444;
            background: rgba(255, 255, 255, 0.02);
        }

        /* Container */
        .main-layout {
            max-width: 1300px;
            width: 100%;
            margin: 0 auto;
            padding: 40px 24px;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 40px;
            flex: 1;
        }

        /* Sidebar Navigation */
        .sidebar {
            position: sticky;
            top: 100px;
            height: calc(100vh - 140px);
            overflow-y: auto;
            padding-right: 16px;
        }
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #222;
            border-radius: 4px;
        }
        .sidebar-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
        }
        .nav-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .nav-item a {
            display: block;
            padding: 10px 14px;
            border-radius: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 2px solid transparent;
        }
        .nav-item a:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.02);
        }
        .nav-item.active a {
            color: #fff;
            background: rgba(255, 255, 255, 0.04);
            border-left-color: var(--accent);
        }

        /* API Content Area */
        .api-content {
            display: flex;
            flex-direction: column;
            gap: 56px;
        }

        .section-header {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .section-desc {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* Endpoint Card */
        .endpoint-card {
            background: var(--card-color);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            margin-bottom: 30px;
            overflow: hidden;
        }

        /* Card Header */
        .endpoint-header {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: rgba(255, 255, 255, 0.01);
            border-bottom: 1px solid var(--border-color);
        }
        .endpoint-route {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .method-badge {
            font-size: 11px;
            font-weight: 800;
            padding: 5px 10px;
            border-radius: 6px;
            width: 70px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            flex-shrink: 0;
        }
        .method-get { background: rgba(16, 185, 129, 0.12); color: var(--get-color); border: 1px solid rgba(16, 185, 129, 0.2); }
        .method-post { background: rgba(59, 130, 246, 0.12); color: var(--post-color); border: 1px solid rgba(59, 130, 246, 0.2); }
        .method-put { background: rgba(245, 158, 11, 0.12); color: var(--put-color); border: 1px solid rgba(245, 158, 11, 0.2); }
        .method-delete { background: rgba(239, 68, 68, 0.12); color: var(--delete-color); border: 1px solid rgba(239, 68, 68, 0.2); }

        .route-path {
            font-family: 'Fira Code', monospace;
            font-size: 14.5px;
            font-weight: 600;
            color: #fff;
            word-break: break-all;
        }
        .route-desc {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Card Details Body */
        .endpoint-details {
            padding: 24px;
            background: rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .detail-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .detail-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .detail-title i {
            color: var(--accent);
        }

        /* Parameters Table */
        .params-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            background: #080808;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }
        .params-table th, .params-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        .params-table th {
            background: #111;
            color: var(--text-primary);
            font-weight: 600;
        }
        .params-table tr:last-child td {
            border-bottom: none;
        }
        .param-name {
            font-family: 'Fira Code', monospace;
            font-weight: 600;
            color: #f43f5e;
        }
        .param-type {
            font-family: 'Fira Code', monospace;
            color: #38bdf8;
        }
        .param-status {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .status-required {
            background: rgba(239, 68, 68, 0.12);
            color: var(--delete-color);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .status-optional {
            background: rgba(163, 163, 163, 0.12);
            color: var(--text-secondary);
            border: 1px solid rgba(163, 163, 163, 0.2);
        }

        /* Codeblock styling */
        .code-container {
            position: relative;
            background: #060606;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            overflow-x: auto;
            max-height: 400px;
        }
        .code-block {
            font-family: 'Fira Code', monospace;
            font-size: 12.5px;
            color: #a5b4fc;
            line-height: 1.6;
            white-space: pre;
        }
        .response-header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #18181b;
            border: 1px solid var(--border-color);
            border-bottom: none;
            border-radius: 10px 10px 0 0;
            padding: 8px 16px;
            font-size: 12px;
            color: var(--text-secondary);
        }
        .response-header-info .status-ok {
            color: var(--get-color);
            font-weight: 700;
        }
        .code-container-with-header {
            border-radius: 0 0 10px 10px;
        }

        @media (max-width: 900px) {
            .main-layout {
                grid-template-columns: 1fr;
            }
            .sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>

    <header>
        <div class="header-container">
            <a href="#" class="logo">
                <i class="fa-solid fa-square-poll-vertical logo-icon"></i>
                <span class="logo-text">Pandara Health API</span>
                <span class="dev-badge">Dokumentasi Lengkap</span>
            </a>
            <div class="header-actions">
                <a href="{{ route('api.docs') }}" class="back-btn">
                    <i class="fa-solid fa-arrow-left"></i> Developer Portal
                </a>
                <a href="{{ route('dashboard') }}" class="back-btn">
                    <i class="fa-solid fa-house"></i> Dashboard Web
                </a>
            </div>
        </div>
    </header>

    <div class="main-layout">
        
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <h3 class="sidebar-title">Kategori API</h3>
            <ul class="nav-list">
                <li class="nav-item active" data-section="auth"><a href="#auth">1. Authentication</a></li>
                <li class="nav-item" data-section="umkm"><a href="#umkm">2. UMKM Management</a></li>
                <li class="nav-item" data-section="assessment"><a href="#assessment">3. Assessment & DSS</a></li>
                <li class="nav-item" data-section="analytics"><a href="#analytics">4. Analytics Dashboard</a></li>
                <li class="nav-item" data-section="admin"><a href="#admin">5. System Admin</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="api-content">

            <!-- ================= AUTHENTICATION ================= -->
            <section id="auth">
                <div class="section-header">
                    <h2 class="section-title">1. Authentication API</h2>
                    <p class="section-desc">API untuk manajemen registrasi, login, session, dan pengambilan profil pengguna Owner.</p>
                </div>

                <!-- POST /api/auth/register -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-post">POST</span>
                            <span class="route-path">{{ url('/api/auth/register') }}</span>
                        </div>
                        <p class="route-desc">Mendaftarkan akun Owner baru ke sistem SiSehat.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-list"></i> Request Body Parameters</h4>
                            <table class="params-table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="param-name">nama</td>
                                        <td class="param-type">string</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>Nama lengkap pendaftar.</td>
                                    </tr>
                                    <tr>
                                        <td class="param-name">email</td>
                                        <td class="param-type">string</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>Email aktif (harus unik).</td>
                                    </tr>
                                    <tr>
                                        <td class="param-name">password</td>
                                        <td class="param-type">string</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>Password akun (min. 8 karakter).</td>
                                    </tr>
                                    <tr>
                                        <td class="param-name">gender</td>
                                        <td class="param-type">string</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>Pilihan jenis kelamin: laki-laki / perempuan (mendukung L / P).</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">201 Created</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "message": "Owner berhasil didaftarkan.",
    "owner": {
        "nama": "Ahmad Dani",
        "email": "ahmaddani@gmail.com",
        "gender": "laki-laki",
        "updated_at": "2026-05-19T14:00:00.000000Z",
        "created_at": "2026-05-19T14:00:00.000000Z",
        "owner_id": 15
    }
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- POST /api/auth/login -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-post">POST</span>
                            <span class="route-path">{{ url('/api/auth/login') }}</span>
                        </div>
                        <p class="route-desc">Autentikasi login Owner untuk memulai sesi pengguna.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-list"></i> Request Body Parameters</h4>
                            <table class="params-table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="param-name">email</td>
                                        <td class="param-type">string</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>Email terdaftar.</td>
                                    </tr>
                                    <tr>
                                        <td class="param-name">password</td>
                                        <td class="param-type">string</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>Password akun.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "message": "Login berhasil.",
    "owner": {
        "owner_id": 1,
        "nama": "Budi Santoso",
        "email": "budi.santoso@gmail.com",
        "gender": "L",
        "foto_profil": null,
        "created_at": "2026-05-01T00:00:00.000000Z",
        "updated_at": "2026-05-01T00:00:00.000000Z"
    }
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- POST /api/auth/logout -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-post">POST</span>
                            <span class="route-path">{{ url('/api/auth/logout') }}</span>
                        </div>
                        <p class="route-desc">Mengeluarkan sesi Owner yang saat ini aktif.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "message": "Logout berhasil."
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GET /api/auth/me -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-get">GET</span>
                            <span class="route-path">{{ url('/api/auth/me') }}</span>
                        </div>
                        <p class="route-desc">Mengambil data profil pengguna Owner yang sedang masuk log.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "owner": {
        "owner_id": 1,
        "nama": "Budi Santoso",
        "email": "budi.santoso@gmail.com",
        "gender": "L",
        "foto_profil": "profiles/photo_1.png",
        "created_at": "2026-05-01T00:00:00.000000Z",
        "updated_at": "2026-05-01T00:00:00.000000Z"
    }
}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <!-- ================= UMKM MANAGEMENT ================= -->
            <section id="umkm">
                <div class="section-header">
                    <h2 class="section-title">2. UMKM Management API</h2>
                    <p class="section-desc">API untuk mendaftarkan, mengambil, memperbarui, dan mengelola unit UMKM milik Owner.</p>
                </div>

                <!-- GET /api/umkm -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-get">GET</span>
                            <span class="route-path">{{ url('/api/umkm') }}</span>
                        </div>
                        <p class="route-desc">Mengambil daftar seluruh unit usaha UMKM yang dimiliki Owner.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-list"></i> Query Parameters</h4>
                            <table class="params-table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="param-name">owner_id</td>
                                        <td class="param-type">integer</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>ID unik Owner untuk menyaring data UMKM miliknya.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "message": "Data UMKM berhasil diambil",
    "data": [
        {
            "umkm_id": "1",
            "owner_id": 1,
            "nama_umkm": "Kopi Pandara",
            "sektor_usaha": "Food and Beverage",
            "umur_usaha": "> 3 tahun",
            "jumlah_karyawan": 15,
            "created_at": "2026-05-05T12:20:03.000000Z",
            "updated_at": "2026-05-10T12:57:24.000000Z"
        }
    ]
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- POST /api/umkm -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-post">POST</span>
                            <span class="route-path">{{ url('/api/umkm') }}</span>
                        </div>
                        <p class="route-desc">Mendaftarkan unit bisnis UMKM baru milik Owner.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-list"></i> Request Body Parameters</h4>
                            <table class="params-table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="param-name">nama_umkm</td>
                                        <td class="param-type">string</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>Nama unit usaha UMKM.</td>
                                    </tr>
                                    <tr>
                                        <td class="param-name">sektor_usaha</td>
                                        <td class="param-type">string</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>Sektor industri (contoh: Food and Beverage, Jasa, retail).</td>
                                    </tr>
                                    <tr>
                                        <td class="param-name">umur_usaha</td>
                                        <td class="param-type">string</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>Lama usaha berdiri (contoh: < 1 tahun, 1 - 3 tahun, > 3 tahun).</td>
                                    </tr>
                                    <tr>
                                        <td class="param-name">jumlah_karyawan</td>
                                        <td class="param-type">integer</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>Jumlah karyawan aktif (minimal 1 orang).</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">201 Created</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "message": "UMKM berhasil ditambahkan.",
    "umkm": {
        "nama_umkm": "Bakery Pandara",
        "sektor_usaha": "Food and Beverage",
        "umur_usaha": "1 - 3 tahun",
        "jumlah_karyawan": 8,
        "owner_id": 1,
        "updated_at": "2026-05-19T14:30:00.000000Z",
        "created_at": "2026-05-19T14:30:00.000000Z",
        "umkm_id": 5
    }
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GET /api/umkm/{id} -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-get">GET</span>
                            <span class="route-path">{{ url('/api/umkm/{id}') }}</span>
                        </div>
                        <p class="route-desc">Mengambil rincian data untuk satu UMKM spesifik berdasarkan ID.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-list"></i> Path Parameters</h4>
                            <table class="params-table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="param-name">id</td>
                                        <td class="param-type">integer</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>ID unik UMKM tujuan.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "message": "Data UMKM berhasil ditemukan",
    "data": {
        "umkm_id": 1,
        "owner_id": 1,
        "nama_umkm": "Kopi Pandara",
        "sektor_usaha": "Food and Beverage",
        "umur_usaha": "> 3 tahun",
        "jumlah_karyawan": 15,
        "created_at": "2026-05-05T12:20:03.000000Z",
        "updated_at": "2026-05-10T12:57:24.000000Z"
    }
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PUT /api/umkm/{id} -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-put">PUT</span>
                            <span class="route-path">{{ url('/api/umkm/{id}') }}</span>
                        </div>
                        <p class="route-desc">Memperbarui data profil UMKM tertentu.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-list"></i> Path & Body Parameters</h4>
                            <table class="params-table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="param-name">id</td>
                                        <td class="param-type">integer</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>ID unik UMKM (Path parameter).</td>
                                    </tr>
                                    <tr>
                                        <td class="param-name">nama_umkm</td>
                                        <td class="param-type">string</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>Nama baru untuk UMKM.</td>
                                    </tr>
                                    <tr>
                                        <td class="param-name">jumlah_karyawan</td>
                                        <td class="param-type">integer</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>Jumlah karyawan terupdate.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "message": "Data UMKM berhasil diperbarui.",
    "umkm": {
        "umkm_id": 1,
        "owner_id": 1,
        "nama_umkm": "Kopi Pandara Premium",
        "sektor_usaha": "Food and Beverage",
        "umur_usaha": "> 3 tahun",
        "jumlah_karyawan": 18,
        "created_at": "2026-05-05T12:20:03.000000Z",
        "updated_at": "2026-05-19T14:35:00.000000Z"
    }
}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <!-- ================= ASSESSMENT & DSS ================= -->
            <section id="assessment">
                <div class="section-header">
                    <h2 class="section-title">3. Assessment & DSS API</h2>
                    <p class="section-desc">API untuk membuat sesi kuesioner baru, memuat pertanyaan, mengirim respons, memantau kemajuan, dan memicu kalkulasi DSS.</p>
                </div>

                <!-- POST /api/assessment/create -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-post">POST</span>
                            <span class="route-path">{{ url('/api/assessment/create') }}</span>
                        </div>
                        <p class="route-desc">Menginisiasi sesi penilaian baru untuk UMKM tertentu.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-list"></i> Request Body Parameters</h4>
                            <table class="params-table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="param-name">umkm_id</td>
                                        <td class="param-type">integer</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>ID unit usaha UMKM target.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">201 Created</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "message": "Sesi assessment berhasil dibuat.",
    "assessment": {
        "umkm_id": 1,
        "status": "in_progress",
        "jumlah_karyawan": 15,
        "updated_at": "2026-05-19T14:40:00.000000Z",
        "created_at": "2026-05-19T14:40:00.000000Z",
        "assessment_id": 10
    }
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GET /api/assessment/questions -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-get">GET</span>
                            <span class="route-path">{{ url('/api/assessment/questions') }}</span>
                        </div>
                        <p class="route-desc">Mengambil daftar pertanyaan instrumen audit SiSehat berdasarkan tipe responden.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-list"></i> Query Parameters</h4>
                            <table class="params-table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="param-name">type</td>
                                        <td class="param-type">string</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>Jenis responden target pertanyaan: <code>owner</code> atau <code>employee</code>.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "status": "success",
    "type": "owner",
    "total_questions": 30,
    "questions": [
        {
            "question_id": 1,
            "factor_id": 1,
            "kode_soal": "Q-001",
            "question_text": "Apakah bisnis Anda memiliki pembukuan keuangan bulanan?",
            "respondent_type": "owner",
            "max_score": 5,
            "created_at": "2026-05-01T00:00:00.000000Z",
            "updated_at": "2026-05-01T00:00:00.000000Z"
        }
    ]
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- POST /api/responses/submit -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-post">POST</span>
                            <span class="route-path">{{ url('/api/responses/submit') }}</span>
                        </div>
                        <p class="route-desc">Mengirimkan berkas jawaban kuesioner dari Owner atau Employee.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-list"></i> Request JSON Body</h4>
                            <div class="code-container">
                                <code class="code-block">{
    "assessment_id": 10,
    "respondent_type": "employee",
    "employee_code": "EMP-005",
    "responses": [
        { "question_id": 1, "answer_value": 4 },
        { "question_id": 2, "answer_value": 5 }
    ]
}</code>
                            </div>
                        </div>
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "message": "Jawaban berhasil disimpan."
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- POST /api/assessment/{id}/calculate -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-post">POST</span>
                            <span class="route-path">{{ url('/api/assessment/{id}/calculate') }}</span>
                        </div>
                        <p class="route-desc">Memicu mesin kalkulasi DSS untuk memproses jawaban, menghitung skor 6 faktor, dan menetapkan kategori kesehatan.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-list"></i> Path Parameters</h4>
                            <table class="params-table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="param-name">id</td>
                                        <td class="param-type">integer</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>ID unik asesmen target kalkulasi.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "message": "Success",
    "data": {
        "health_score_id": 5,
        "assessment_id": 10,
        "overall_score": 78.4,
        "category": "SEHAT",
        "calculated_at": "2026-05-19T14:45:00.000000Z",
        "created_at": "2026-05-19T14:45:00.000000Z",
        "updated_at": "2026-05-19T14:45:00.000000Z"
    }
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GET /api/assessment/{id}/monitoring -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-get">GET</span>
                            <span class="route-path">{{ url('/api/assessment/{id}/monitoring') }}</span>
                        </div>
                        <p class="route-desc">Memantau jumlah partisipasi pengisian kuesioner dan aktivitas pengisian terbaru.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-list"></i> Path Parameters</h4>
                            <table class="params-table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="param-name">id</td>
                                        <td class="param-type">integer</td>
                                        <td><span class="param-status status-required">Wajib</span></td>
                                        <td>ID unik asesmen.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "total_respondents": 12,
    "estimated_score": 75.6,
    "estimated_category": "SEHAT",
    "recent_activity": [
        {
            "employee_code": "EMP-005",
            "last_active": "2026-05-19 21:30:15"
        },
        {
            "employee_code": "EMP-002",
            "last_active": "2026-05-19 21:28:44"
        }
    ]
}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <!-- ================= ANALYTICS DASHBOARD ================= -->
            <section id="analytics">
                <div class="section-header">
                    <h2 class="section-title">4. Analytics Dashboard API</h2>
                    <p class="section-desc">API penyuplai data hasil pengolahan statistik untuk visualisasi visual, radar chart, tren linier, dan analisis pencilan boxplot.</p>
                </div>

                <!-- GET /api/dashboard/umkm/{id}/latest -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-get">GET</span>
                            <span class="route-path">{{ url('/api/dashboard/umkm/{id}/latest') }}</span>
                        </div>
                        <p class="route-desc">Mengambil skor rata-rata per-faktor hasil kalkulasi asesmen terbaru untuk grafik radar.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "status": "success",
    "assessment_id": 10,
    "overall_score": 78.4,
    "category": "SEHAT",
    "factors": [
        {
            "nama_factor": "Kondisi Finansial",
            "score": 85.0
        },
        {
            "nama_factor": "Kepemimpinan & SDM",
            "score": 72.5
        },
        {
            "nama_factor": "Operasional Bisnis",
            "score": 76.8
        },
        {
            "nama_factor": "Adaptasi Teknologi",
            "score": 60.0
        },
        {
            "nama_factor": "Fokus Pelanggan",
            "score": 90.2
        },
        {
            "nama_factor": "Strategi Pertumbuhan",
            "score": 86.1
        }
    ]
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GET /api/dashboard/umkm/{id}/trend -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-get">GET</span>
                            <span class="route-path">{{ url('/api/dashboard/umkm/{id}/trend') }}</span>
                        </div>
                        <p class="route-desc">Mengambil deret historis skor keseluruhan antar periode survei untuk grafik tren.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "status": "success",
    "umkm_id": 1,
    "trend": [
        {
            "assessment_id": 8,
            "overall_score": 72.1,
            "category": "CUKUP_SEHAT",
            "date": "2026-05-01"
        },
        {
            "assessment_id": 10,
            "overall_score": 78.4,
            "category": "SEHAT",
            "date": "2026-05-19"
        }
    ]
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GET /api/dashboard/assessment/{id}/factors -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-get">GET</span>
                            <span class="route-path">{{ url('/api/dashboard/assessment/{id}/factors') }}</span>
                        </div>
                        <p class="route-desc">Mengambil nilai rata-rata skor per-faktor kesehatan dan kategorinya untuk monitoring detail.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "status": "success",
    "assessment_id": 10,
    "factors": [
        {
            "factor_id": 1,
            "nama_factor": "Kondisi Finansial",
            "score": 85.0,
            "category": "SEHAT"
        },
        {
            "factor_id": 2,
            "nama_factor": "Kepemimpinan & SDM",
            "score": 72.5,
            "category": "SEHAT"
        }
    ]
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GET /api/dashboard/assessment/{id}/details -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-get">GET</span>
                            <span class="route-path">{{ url('/api/dashboard/assessment/{id}/details') }}</span>
                        </div>
                        <p class="route-desc">Mengambil breakdown jawaban per-soal beserta komparasi penilaian Owner & Employee.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "status": "success",
    "assessment_id": 10,
    "details": [
        {
            "question_id": 1,
            "kode_soal": "Q-001",
            "question_text": "Apakah bisnis Anda memiliki pembukuan keuangan bulanan?",
            "factor_name": "Kondisi Finansial",
            "owner_response": 5,
            "employee_avg_response": 4.2,
            "blended_score": 4.6,
            "normalized_score": 92.0
        }
    ]
}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GET /api/dashboard/assessment/{id}/outliers -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-get">GET</span>
                            <span class="route-path">{{ url('/api/dashboard/assessment/{id}/outliers') }}</span>
                        </div>
                        <p class="route-desc">Mengambil nilai lima serangkai statistik (min, q1, median, q3, max) untuk menggambar Boxplot pencilan.</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "status": "success",
    "assessment_id": 10,
    "boxplot_data": [
        {
            "factor_name": "Kondisi Finansial",
            "min": 1.0,
            "q1": 3.0,
            "median": 4.0,
            "q3": 5.0,
            "max": 5.0,
            "outliers": [1.0]
        }
    ]
}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <!-- ================= SYSTEM ADMIN ================= -->
            <section id="admin">
                <div class="section-header">
                    <h2 class="section-title">5. System Admin API</h2>
                    <p class="section-desc">API utilitas administrasi internal sistem yang berguna untuk lingkungan produksi.</p>
                </div>

                <!-- GET /run-migration -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <div class="endpoint-route">
                            <span class="method-badge method-get">GET</span>
                            <span class="route-path">{{ url('/run-migration') }}</span>
                        </div>
                        <p class="route-desc">Menjalankan migrasi database paksa dan impor seeder data awal via HTTP (Tanpa SSH).</p>
                    </div>
                    <div class="endpoint-details">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fa-solid fa-reply"></i> Respons JSON Lengkap</h4>
                            <div class="response-header-info">
                                <span>Content-Type: <code>application/json</code></span>
                                <span class="status-ok">200 OK</span>
                            </div>
                            <div class="code-container code-container-with-header">
                                <code class="code-block">{
    "status": "success",
    "message": "Migrasi database dan impor CSV berhasil dilakukan!",
    "migrate_output": "Migration table created successfully. ...",
    "import_output": "CSV Question seeder imported successfully. ..."
}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

    <!-- Script for highlighting active sidebar item based on scroll -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const sections = document.querySelectorAll("section");
            const navItems = document.querySelectorAll(".nav-item");

            window.addEventListener("scroll", () => {
                let currentSectionId = "";
                sections.forEach(section => {
                    const sectionTop = section.offsetTop - 140;
                    if (window.scrollY >= sectionTop) {
                        currentSectionId = section.getAttribute("id");
                    }
                });

                if (currentSectionId) {
                    navItems.forEach(item => {
                        item.classList.remove("active");
                        if (item.getAttribute("data-section") === currentSectionId) {
                            item.classList.add("active");
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
