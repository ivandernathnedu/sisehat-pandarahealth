@extends('layouts.clean')

@section('title', 'Pendataan Organisasi')

@section('styles')
<style>
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-secondary);
        text-decoration: none;
        margin-bottom: 28px;
        cursor: pointer;
        transition: color 0.2s;
    }
    .back-link:hover { color: #fff; }
    .back-link i { font-size: 12px; }

    .page-heading { font-size: 32px; font-weight: 700; margin-bottom: 14px; letter-spacing: -0.5px; }
    .page-desc {
        font-size: 14px;
        color: var(--text-secondary);
        line-height: 1.7;
        margin-bottom: 36px;
        max-width: 520px;
    }

    /* Form Card */
    .form-card {
        background: #1a1a1a;
        border-radius: 20px;
        padding: 44px 48px 40px;
    }

    .form-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 36px;
    }

    .form-card-title .title-icon {
        font-size: 20px;
        color: var(--text-secondary);
    }

    /* Form fields */
    .field-group { margin-bottom: 28px; }
    .field-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: var(--text-secondary);
        margin-bottom: 10px;
    }

    .field-input, .field-select {
        width: 100%;
        background: #0f0f0f;
        border: 1px solid #2a2a2a;
        border-radius: 10px;
        padding: 16px 18px;
        color: #fff;
        font-size: 14px;
        font-family: 'Inter', sans-serif;
        outline: none;
        transition: border-color 0.2s;
        appearance: none;
        -webkit-appearance: none;
    }

    .field-input:focus, .field-select:focus { border-color: #444; }
    .field-input::placeholder { color: #444; }
    .field-input[value="0"] { color: #666; }

    .field-select-wrap { position: relative; }
    .field-select-wrap .chevron {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        pointer-events: none;
        font-size: 13px;
    }

    .field-select option { background: #1a1a1a; color: #fff; }

    /* 2 column row */
    .field-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    /* Buttons */
    .form-actions {
        display: flex;
        justify-content: center;
        gap: 16px;
        margin-top: 40px;
    }

    .btn-batal {
        background: transparent;
        border: 1px solid #333;
        border-radius: 50px;
        padding: 14px 48px;
        color: var(--text-secondary);
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        transition: all 0.2s;
    }
    .btn-batal:hover { border-color: #555; color: #fff; }

    .btn-simpan {
        background: #fff;
        border: none;
        border-radius: 50px;
        padding: 14px 36px;
        color: #000;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }
    .btn-simpan:hover { background: #e5e5e5; }

    /* Custom Radio Styles */
    .radio-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 8px;
    }
    .radio-item {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 12px 16px;
        background: #0f0f0f;
        border: 1px solid #2a2a2a;
        border-radius: 10px;
        transition: all 0.2s;
    }
    .radio-item:hover { border-color: #444; background: #151515; }
    .radio-item input[type="radio"] {
        appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid #444;
        border-radius: 50%;
        outline: none;
        cursor: pointer;
        position: relative;
        transition: border-color 0.2s;
    }
    .radio-item input[type="radio"]:checked { border-color: #fff; }
    .radio-item input[type="radio"]:checked::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 8px;
        height: 8px;
        background: #fff;
        border-radius: 50%;
    }
    .radio-label { font-size: 14px; color: #fff; }

    @media (max-width: 768px) {
        .form-card { padding: 32px 24px; }
        .field-row { grid-template-columns: 1fr; gap: 16px; }
    }
    
    @media (max-width: 480px) {
        .page-heading { font-size: 24px; }
        .form-actions { flex-direction: column; width: 100%; }
        .btn-batal, .btn-simpan { width: 100%; text-align: center; justify-content: center; }
    }
</style>
@endsection

@section('content')

<!-- Back Link -->
<a href="{{ route('assessment') }}" class="back-link">
    <i class="fa-solid fa-arrow-left"></i>
    Kembali
</a>

<!-- Page Header -->
<h1 class="page-heading">Pendataan Organisasi</h1>
<p class="page-desc">
    Silakan lengkapi profil fundamental bisnis Anda untuk memulai proses asesmen kesehatan
    organisasi. Data ini digunakan untuk memberikan rekomendasi yang presisi sesuai dengan
    skala dan industri UMKM Anda.
</p>

<!-- Form Card -->
<div class="form-card">
    <div class="form-card-title">
        <i class="fa-solid fa-building-columns title-icon"></i>
        <span>Identitas UMKM</span>
    </div>

    <form id="tambahUmkmForm" action="{{ route('tambah-umkm.post') }}" method="POST">
        @csrf
        <!-- Nama UMKM -->
        <div class="field-group">
            <label class="field-label">Nama UMKM</label>
            <input
                type="text"
                name="nama_umkm"
                class="field-input"
                placeholder="Masukkan nama entitas bisnis Anda"
                required
            >
        </div>

        <!-- Umur Perusahaan + Sektor Industri -->
        <div class="field-row">
            <div class="field-group" style="margin-bottom:0;">
                <label class="field-label">Umur Perusahaan</label>
                <div class="field-select-wrap">
                    <select class="field-select" name="umur_usaha" required>
                        <option value="" disabled selected>Pilih umur perusahaan...</option>
                        <option value="< 1 tahun">< 1 tahun</option>
                        <option value="1 - 3 tahun">1 - 3 tahun</option>
                        <option value="> 3 tahun">> 3 tahun</option>
                    </select>
                    <i class="fa-solid fa-chevron-down chevron"></i>
                </div>
            </div>
            <div class="field-group" style="margin-bottom:0;">
                <label class="field-label">Sektor Industri</label>
                <div class="field-select-wrap">
                    <select class="field-select" name="sektor_usaha" required>
                        <option value="" disabled selected>Pilih sektor industri...</option>
                        <option value="Food and Beverage">Food and Beverage</option>
                        <option value="Fashion">Fashion</option>
                        <option value="Wholesale and retail">Wholesale and retail</option>
                    </select>
                    <i class="fa-solid fa-chevron-down chevron"></i>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="form-actions">
            <button type="button" class="btn-batal" onclick="window.location='{{ route('assessment') }}'">
                Batal
            </button>
            <button type="submit" class="btn-simpan">
                Simpan Profil <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    // Form akan dikirim secara normal ke backend
</script>
@endsection
