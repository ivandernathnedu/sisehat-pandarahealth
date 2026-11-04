@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
<h1 class="auth-title">Selamat Datang</h1>
<p class="auth-subtitle">Masuk ke sistem analitik kesehatan Anda.</p>

<form method="POST" action="{{ route('login.post') }}">
    @csrf

    {{-- Email --}}
    <div class="field">
        <label class="field-label">Alamat Email</label>
        <div class="field-inner">
            <span class="field-icon"><i class="fa-regular fa-envelope"></i></span>
            <input type="email" name="email" class="field-input" placeholder="nama@email.com" autocomplete="email" value="{{ old('email') }}">
        </div>
        @error('email')
            <span style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Password --}}
    <div class="field">
        <div class="field-row">
            <label class="field-label">Kata Sandi</label>
            <a href="{{ route('profile.password') }}" class="forgot-link">Lupa Password?</a>
        </div>
        <div class="field-inner">
            <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="password" class="field-input" placeholder="••••••••" id="loginPassword">
            <span class="field-right" onclick="togglePass('loginPassword', this)">
                <i class="fa-regular fa-eye-slash"></i>
            </span>
        </div>
        @error('password')
            <span style="color: var(--danger); font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
        @enderror
        @if(session('error'))
            <span style="color: var(--danger); font-size: 12px; margin-top: 4px; display: block;">{{ session('error') }}</span>
        @endif
    </div>

    <button type="submit" class="btn-submit">
        MASUK <i class="fa-solid fa-arrow-right"></i>
    </button>
</form>

<p class="auth-footer">
    Belum memiliki akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
</p>

<div style="text-align: center; margin-top: 24px;">
    <a href="{{ route('welcome') }}" style="color: #737373; text-decoration: none; font-size: 13px; font-weight: 600;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
    </a>
</div>

<script>
function togglePass(id, el) {
    const input = document.getElementById(id);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    el.innerHTML = isHidden
        ? '<i class="fa-regular fa-eye"></i>'
        : '<i class="fa-regular fa-eye-slash"></i>';
}
</script>
@endsection
