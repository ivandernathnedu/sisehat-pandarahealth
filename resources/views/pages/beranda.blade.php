@extends('layouts.app')

@section('title', 'Beranda')

@section('styles')
<style>
    /* ── Hero Grid ── */
    .hero-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 32px;
    }

    .hero-card {
        background: var(--card-color);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 32px;
    }

    .ai-badge {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(255,255,255,0.06); border: 1px solid #2a2a2a;
        color: #aaa; font-size: 11px; font-weight: 600;
        padding: 5px 12px; border-radius: 20px;
        margin-bottom: 20px; letter-spacing: 0.3px;
    }
    .ai-badge::before {
        content: '✦'; color: #4ade80; font-size: 10px;
    }

    .hero-brand { font-size: 16px; font-weight: 600; margin-bottom: 8px; }
    .hero-tagline { font-size: 15px; font-weight: 700; margin-bottom: 16px; }
    .hero-desc {
        font-size: 13px; color: var(--text-secondary);
        line-height: 1.75; margin-bottom: 28px;
    }
    .hero-actions { display: flex; gap: 12px; }
    .btn-primary-hero {
        display: inline-flex; align-items: center; gap: 8px;
        background: #fff; color: #000; border: none;
        border-radius: 8px; padding: 11px 20px;
        font-size: 13px; font-weight: 600;
        cursor: pointer; font-family: 'Inter', sans-serif;
        transition: background 0.2s;
    }
    .btn-primary-hero:hover { background: #e5e5e5; }
    .btn-ghost-hero {
        display: inline-flex; align-items: center; gap: 8px;
        background: transparent; color: #fff;
        border: 1px solid var(--border-color);
        border-radius: 8px; padding: 11px 20px;
        font-size: 13px; font-weight: 500;
        cursor: pointer; font-family: 'Inter', sans-serif;
        transition: border-color 0.2s, background 0.2s;
    }
    .btn-ghost-hero:hover { border-color: #555; background: #111; }

    /* ── Right "Mulai Kelola" card ── */
    .kelola-card {
        background: var(--card-color);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 32px;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        text-align: center; gap: 16px;
    }
    .kelola-icon-wrap {
        width: 72px; height: 72px; border-radius: 50%;
        background: #1e1e1e; border: 1px solid #2a2a2a;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; color: #fff; margin-bottom: 8px;
    }
    .kelola-title { font-size: 22px; font-weight: 700; margin-bottom: 8px; }
    .kelola-desc { font-size: 13px; color: var(--text-secondary); line-height: 1.65; margin-bottom: 20px; max-width: 220px; }
    .btn-tambah-umkm {
        display: inline-flex; align-items: center; gap: 8px;
        background: #fff; color: #000; border: none;
        border-radius: 10px; padding: 13px 28px;
        font-size: 14px; font-weight: 600;
        cursor: pointer; font-family: 'Inter', sans-serif;
        transition: background 0.2s, transform 0.15s;
    }
    .btn-tambah-umkm:hover { background: #e5e5e5; transform: translateY(-1px); }

    /* ── Features ── */
    .section-label-sm {
        font-size: 13px; font-weight: 600; margin-bottom: 6px;
    }
    .section-desc-sm {
        font-size: 13px; color: var(--text-secondary); margin-bottom: 24px; line-height: 1.6;
    }
    .features-grid {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 14px; margin-bottom: 40px;
    }
    .feature-card {
        background: var(--card-color); border: 1px solid var(--border-color);
        border-radius: 14px; padding: 22px 18px;
        transition: border-color 0.2s, transform 0.2s; cursor: default;
    }
    .feature-card:hover { border-color: #333; transform: translateY(-3px); }
    .feature-icon {
        width: 40px; height: 40px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 14px; font-size: 16px;
    }
    .icon-indigo { background: rgba(99,102,241,0.15); color: #818cf8; }
    .icon-teal   { background: rgba(20,184,166,0.15);  color: #2dd4bf; }
    .icon-amber  { background: rgba(245,158,11,0.15);  color: #fbbf24; }
    .icon-violet { background: rgba(139,92,246,0.15);  color: #a78bfa; }
    .feature-title { font-size: 14px; font-weight: 600; margin-bottom: 8px; }
    .feature-desc  { font-size: 12px; color: var(--text-secondary); line-height: 1.7; }

    /* ── How it works ── */
    .how-section { text-align: center; padding: 40px 0 0; border-top: 1px solid var(--border-color); }
    .how-title { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
    .how-desc  { font-size: 13px; color: var(--text-secondary); margin-bottom: 36px; }
    .steps-grid {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 20px; position: relative;
    }
    .steps-grid::before {
        content: ''; position: absolute;
        top: 32px; left: calc(16.66% + 16px); right: calc(16.66% + 16px);
        height: 1px; background: var(--border-color); z-index: 0;
    }
    .step-card {
        background: var(--card-color); border: 1px solid var(--border-color);
        border-radius: 14px; padding: 28px 24px; text-align: center; position: relative; z-index: 1;
    }
    .step-num {
        width: 52px; height: 52px; border-radius: 50%;
        border: 1px solid var(--border-color); background: var(--bg-color);
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; color: var(--text-secondary);
        margin: 0 auto 20px;
    }
    .step-title { font-size: 14px; font-weight: 600; margin-bottom: 10px; }
    .step-desc  { font-size: 12px; color: var(--text-secondary); line-height: 1.7; }

    /* ── Responsive Media Queries ── */
    @media (max-width: 1024px) {
        .features-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
        .hero-grid { grid-template-columns: 1fr; }
        .hero-actions { flex-direction: column; }
        .btn-primary-hero, .btn-ghost-hero { width: 100%; justify-content: center; }
        
        .steps-grid { grid-template-columns: 1fr; }
        .steps-grid::before { display: none; } /* Hide connecting line on mobile */
    }

    @media (max-width: 480px) {
        .features-grid { grid-template-columns: 1fr; }
        .hero-card, .kelola-card { padding: 24px 20px; }
        .kelola-title { font-size: 20px; }
    }
</style>
@endsection

@section('content')

{{-- ── HERO GRID ── --}}
<div class="hero-grid">

    {{-- Left: Intro CTA --}}
    <div class="hero-card">
        <div class="hero-brand">Pandara Health System</div>
        <div class="hero-tagline">Platform Analisis Kesehatan Organisasi UMKM Berbasis Data.</div>
        <p class="hero-desc">
            Ukur dan optimalkan kesehatan struktural dan kultural bisnis Anda. Sistem kami memetakan kekuatan tersembunyi dan mengidentifikasi area risiko menggunakan model analitik canggih, memberikan wawasan yang dapat ditindaklanjuti untuk pertumbuhan berkelanjutan.
        </p>
        <div class="hero-actions">
            <button class="btn-primary-hero" onclick="location.href='{{ route('assessment') }}'">
                <i class="fa-solid fa-bolt"></i> Mulai Assessment
            </button>
            <button class="btn-ghost-hero" onclick="location.href='{{ route('dashboard') }}'">
                <i class="fa-regular fa-circle-play"></i> Lihat Dashboard
            </button>
        </div>
    </div>

    {{-- Right: Quick Start --}}
    <div class="kelola-card">
        <div class="kelola-icon-wrap">
            <i class="fa-solid fa-building-circle-arrow-right"></i>
        </div>
        <div class="kelola-title">Mulai Kelola Bisnis</div>
        <p class="kelola-desc">Tambahkan profil UMKM baru untuk memulai analisis kesehatan organisasi.</p>
        <button class="btn-tambah-umkm" onclick="location.href='{{ route('tambah-umkm') }}'">
            <i class="fa-solid fa-plus"></i> Tambah UMKM
        </button>
    </div>
</div>

{{-- ── FEATURES ── --}}
<div style="margin-bottom: 8px;">
    <div class="section-label-sm">Apa itu Pandara Health?</div>
    <p class="section-desc-sm">Platform komprehensif yang menerjemahkan data operasional dan persepsi karyawan menjadi metrik kesehatan bisnis yang terukur.</p>
</div>

<div class="features-grid">
    <div class="feature-card">
        <div class="feature-icon icon-indigo"><i class="fa-solid fa-chart-bar"></i></div>
        <div class="feature-title">Insight Bisnis</div>
        <div class="feature-desc">Dapatkan wawasan mendalam tentang kondisi riil organisasi Anda di luar metrik finansial standar.</div>
    </div>
    <div class="feature-card">
        <div class="feature-icon icon-teal"><i class="fa-solid fa-eye"></i></div>
        <div class="feature-title">Monitoring Kinerja</div>
        <div class="feature-desc">Pantau fluktuasi produktivitas dan kesejahteraan tim secara real-time melalui dashboard intuitif.</div>
    </div>
    <div class="feature-card">
        <div class="feature-icon icon-amber"><i class="fa-solid fa-lightbulb"></i></div>
        <div class="feature-title">Rekomendasi Strategis</div>
        <div class="feature-desc">Terima saran tindakan berbasis AI yang disesuaikan spesifik untuk memperbaiki area yang lemah.</div>
    </div>
    <div class="feature-card">
        <div class="feature-icon icon-violet"><i class="fa-solid fa-database"></i></div>
        <div class="feature-title">Keputusan Berbasis Data</div>
        <div class="feature-desc">Ubah asumsi menjadi kepastian dengan mengandalkan analitik kuantitatif untuk keputusan SDM.</div>
    </div>
</div>

{{-- ── HOW IT WORKS ── --}}
<div class="how-section">
    <div class="how-title">Cara Kerja</div>
    <p class="how-desc">Transformasi organisasi Anda hanya dalam tiga langkah sistematis.</p>
    <div class="steps-grid">
        <div class="step-card">
            <div class="step-num">01</div>
            <div class="step-title">Isi Assessment</div>
            <div class="step-desc">Tim Anda menyelesaikan kuesioner terstruktur yang dirancang secara ilmiah untuk memetakan dinamika internal.</div>
        </div>
        <div class="step-card">
            <div class="step-num">02</div>
            <div class="step-title">Analisis Faktor</div>
            <div class="step-desc">Sistem AI kami memproses data mentah ke dalam 6 dimensi faktor kunci kesehatan operasional.</div>
        </div>
        <div class="step-card">
            <div class="step-num">03</div>
            <div class="step-title">Dapatkan Rekomendasi</div>
            <div class="step-desc">Terima dashboard visual komprehensif lengkap dengan blueprint tindakan korektif yang terprioritasi.</div>
        </div>
    </div>
</div>

@endsection
