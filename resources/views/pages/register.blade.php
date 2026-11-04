@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
<h1 class="auth-title">Buat Akun</h1>
<p class="auth-subtitle">Daftarkan diri Anda untuk mengakses sistem<br>analitik klinis.</p>

<form method="POST" action="{{ route('register.post') }}">
    @csrf

    {{-- Nama Lengkap --}}
    <div class="field">
        <label class="field-label">Nama Lengkap</label>
        <div class="field-inner">
            <span class="field-icon"><i class="fa-regular fa-user"></i></span>
            <input type="text" name="name" class="field-input" placeholder="Contoh: Dr. Budi Santoso" autocomplete="name" value="{{ old('name') }}">
        </div>
        @error('name')
            <span style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Email --}}
    <div class="field">
        <label class="field-label">Email</label>
        <div class="field-inner">
            <span class="field-icon"><i class="fa-regular fa-envelope"></i></span>
            <input type="email" name="email" class="field-input" placeholder="email@email.com" autocomplete="email" value="{{ old('email') }}">
        </div>
        @error('email')
            <span style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Jenis Kelamin --}}
    <div class="field">
        <label class="field-label">Jenis Kelamin</label>
        <div style="display: flex; gap: 20px; margin-top: 8px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: white;">
                <input type="radio" name="gender" value="laki-laki" {{ old('gender') == 'laki-laki' ? 'checked' : '' }} required>
                Laki-laki
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: white;">
                <input type="radio" name="gender" value="perempuan" {{ old('gender') == 'perempuan' ? 'checked' : '' }}>
                Perempuan
            </label>
        </div>
        @error('gender')
            <span style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Password --}}
    <div class="field">
        <label class="field-label">Kata Sandi</label>
        <div class="field-inner">
            <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="password" class="field-input" placeholder="••••••••" id="regPassword">
        </div>
        @error('password')
            <span style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Confirm Password --}}
    <div class="field">
        <label class="field-label">Konfirmasi Kata Sandi</label>
        <div class="field-inner">
            <span class="field-icon"><i class="fa-solid fa-lock-open"></i></span>
            <input type="password" name="password_confirmation" class="field-input" placeholder="••••••••" id="regPasswordConfirm">
        </div>
    </div>

    <button type="submit" class="btn-submit">
        DAFTAR <i class="fa-solid fa-arrow-right"></i>
    </button>
</form>

<p class="auth-footer">
    Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
</p>

<div style="text-align: center; margin-top: 24px;">
    <a href="{{ route('welcome') }}" style="color: #737373; text-decoration: none; font-size: 13px; font-weight: 600;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
    </a>
</div>
@endsection
