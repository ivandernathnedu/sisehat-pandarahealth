@extends('layouts.app')

@section('title', 'Ubah Nama Profil')

@section('content')
<style>
    .auth-card {
        max-width: 450px; margin: 60px auto;
        background: var(--card-color); border: 1px solid var(--border-color);
        padding: 40px; border-radius: 20px;
    }
    .auth-header { text-align: center; margin-bottom: 32px; }
    .auth-header i { font-size: 32px; color: #555; margin-bottom: 16px; }
    .auth-header h1 { font-size: 20px; font-weight: 600; }
    .auth-header p { font-size: 13px; color: var(--text-secondary); margin-top: 8px; }

    .field { margin-bottom: 20px; }
    .field-label { display: block; font-size: 12px; font-weight: 600; color: #555; margin-bottom: 8px; text-transform: uppercase; }
    .field-inner { position: relative; }
    .field-input {
        width: 100%; background: rgba(255,255,255,0.03);
        border: 1px solid var(--border-color); border-radius: 10px;
        padding: 12px 16px; color: #fff; font-size: 14px;
        transition: all 0.2s;
    }
    .field-input:focus { border-color: #555; outline: none; background: rgba(255,255,255,0.05); }

    .btn-submit {
        width: 100%; background: #fff; color: #000;
        border: none; border-radius: 10px; padding: 14px;
        font-size: 14px; font-weight: 600; cursor: pointer;
        margin-top: 12px; transition: background 0.2s;
    }
    .btn-submit:hover { background: #e5e5e5; }
    
    .btn-back {
        display: block; text-align: center; margin-top: 20px;
        color: var(--text-secondary); font-size: 13px; text-decoration: none;
    }
    .btn-back:hover { color: #fff; }

    .error-msg { color: #f87171; font-size: 12px; margin-top: 4px; }

    @media (max-width: 480px) {
        .auth-card { padding: 32px 20px; margin: 30px auto; }
        .auth-header h1 { font-size: 18px; }
    }
</style>

<div class="auth-card">
    <div class="auth-header">
        <i class="fa-solid fa-user-pen"></i>
        <h1>Identitas Profil</h1>
        <p>Silakan masukkan nama profil baru Anda.</p>
    </div>

    <form method="POST" action="{{ route('profile.name.post') }}">
        @csrf

        {{-- New Name --}}
        <div class="field">
            <label class="field-label">Nama Lengkap / Username</label>
            <div class="field-inner">
                <input type="text" name="name" class="field-input" value="{{ old('name', $owner->name) }}" placeholder="Masukkan nama baru" required autofocus>
            </div>
            @error('name')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">SIMPAN PERUBAHAN</button>
        
        <a href="{{ route('profile') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Profil
        </a>
    </form>
</div>
@endsection
