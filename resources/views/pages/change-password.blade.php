@extends('layouts.auth')

@section('title', 'Ubah Password')

@section('content')
<div style="text-align: center; margin-bottom: 24px;">
    <i class="fa-solid fa-shield-halved" style="font-size: 32px; color: #555; margin-bottom: 16px;"></i>
    <h1 class="auth-title">Keamanan Akun</h1>
    <p class="auth-subtitle">Silakan masukkan password baru Anda untuk menjaga keamanan akun.</p>
</div>

<form method="POST" action="{{ route('profile.password.post') }}">
    @csrf

    @guest('owner')
    {{-- Email untuk guest (Lupa Password) --}}
    <div class="field">
        <label class="field-label">Alamat Email Terdaftar</label>
        <div class="field-inner">
            <span class="field-icon"><i class="fa-regular fa-envelope"></i></span>
            <input type="email" name="email" class="field-input" placeholder="nama@email.com" required>
        </div>
        @error('email')
            <span style="color: #f87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
        @enderror
    </div>
    @endguest

    {{-- New Password --}}
    <div class="field">
        <label class="field-label">Password Baru</label>
        <div class="field-inner">
            <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
            <input type="password" id="newPassword" name="password" class="field-input" placeholder="Minimal 6 karakter" required>
        </div>
        <span id="passLengthMsg" style="font-size: 11px; margin-top: 6px; display: block; color: var(--text-secondary);"></span>
        @error('password')
            <span style="color: #f87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Confirm Password --}}
    <div class="field">
        <label class="field-label">Konfirmasi Password Baru</label>
        <div class="field-inner">
            <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
            <input type="password" id="confirmPassword" name="password_confirmation" class="field-input" placeholder="Ulangi password baru" required>
        </div>
        <span id="passMatchMsg" style="font-size: 11px; margin-top: 6px; display: block; color: var(--text-secondary);"></span>
    </div>

    <button type="submit" class="btn-submit">
        SIMPAN PERUBAHAN <i class="fa-solid fa-check"></i>
    </button>
    
    <div style="text-align: center; margin-top: 24px;">
        @auth('owner')
            <a href="{{ route('profile') }}" style="color: #737373; text-decoration: none; font-size: 13px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        @else
            <a href="{{ route('login') }}" style="color: #737373; text-decoration: none; font-size: 13px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        @endauth
    </div>
</form>
@endsection

@section('scripts')
<script>
    const newPass = document.getElementById('newPassword');
    const confirmPass = document.getElementById('confirmPassword');
    const lengthMsg = document.getElementById('passLengthMsg');
    const matchMsg = document.getElementById('passMatchMsg');
    const submitBtn = document.querySelector('.btn-submit');

    function validatePassword() {
        let isLengthValid = false;
        let isMatchValid = false;

        // Validasi Panjang Password
        if (newPass.value.length === 0) {
            lengthMsg.textContent = "";
            newPass.style.borderColor = "";
        } else if (newPass.value.length < 6) {
            lengthMsg.textContent = "Minimal 6 karakter";
            lengthMsg.style.color = "#f87171"; // Merah
            newPass.style.borderColor = "#f87171";
        } else {
            lengthMsg.textContent = "Panjang password sesuai";
            lengthMsg.style.color = "#4ade80"; // Hijau
            newPass.style.borderColor = "#4ade80";
            isLengthValid = true;
        }

        // Validasi Kecocokan Konfirmasi Password
        if (confirmPass.value.length === 0) {
            matchMsg.textContent = "";
            confirmPass.style.borderColor = "";
        } else if (confirmPass.value !== newPass.value) {
            matchMsg.textContent = "Konfirmasi password tidak cocok";
            matchMsg.style.color = "#f87171"; // Merah
            confirmPass.style.borderColor = "#f87171";
        } else {
            matchMsg.textContent = "Password cocok";
            matchMsg.style.color = "#4ade80"; // Hijau
            confirmPass.style.borderColor = "#4ade80";
            isMatchValid = true;
        }

        // Nonaktifkan tombol jika tidak valid
        if (newPass.value.length > 0 && (!isLengthValid || !isMatchValid)) {
            submitBtn.style.opacity = "0.5";
            submitBtn.style.cursor = "not-allowed";
            submitBtn.disabled = true;
        } else {
            submitBtn.style.opacity = "1";
            submitBtn.style.cursor = "pointer";
            submitBtn.disabled = false;
        }
    }

    newPass.addEventListener('keyup', validatePassword);
    confirmPass.addEventListener('keyup', validatePassword);
</script>
@endsection
