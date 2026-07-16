@extends('layouts.guest')
@section('title', 'Daftar Akun Perangkat')
@section('body_class', 'login-page-split')

@section('content')
    <div class="login-split-container">
        <div class="login-split-brand d-flex flex-column"
            style="min-height: 100vh; position: relative; padding-top: 10%; padding-left: 40px; padding-right: 40px;">
            <div class="brand-showcase">
                <div class="logo-container d-flex align-items-center mb-4" style="gap: 15px;">
                    <img src="{{ asset('images/blitar no bg.png') }}" alt="Logo Kab Blitar"
                        style="height: 65px; width: auto; object-fit: contain;">

                    <img src="{{ asset('images/LOGO DESA NGADRI NO BG.png') }}" alt="Logo {{ config('desa.nama') }}"
                        style="height: 75px; width: auto; object-fit: contain;">

                    <img src="{{ asset('images/Logo Polosan.png') }}" alt="Logo KKN"
                        style="height: 75px; width: auto; object-fit: contain;">
                </div>

                <h1>Pelayanan Surat Menyurat Desa Ngadri</h1>
                <p>Sistem Administrasi Surat Menyurat Terpadu. Saling berkolaborasi memberikan pelayanan administrasi
                    terbaik untuk masyarakat Desa Ngadri.</p>
            </div>

            <div class="brand-showcase-footer" style="position: absolute; bottom: 20px; left: 40px;">
                &copy; 2026 {{ config('desa.nama') }}. All rights reserved.
            </div>
        </div>

        <div class="login-split-form d-flex flex-column" style="padding-top: 5%; overflow-y: auto;">
            <div class="login-form-inner" style="padding-bottom: 2rem;">
                <h2 class="login-welcome-title">Buat Akun Perangkat</h2>
                <p class="login-welcome-subtitle">Masukkan data perangkat desa untuk mendaftarkan akun administrator baru.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 mb-4" style="border-radius: 8px;">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        <ul class="mb-0 pl-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="post">
                    @csrf
                    <div class="split-input-group">
                        <input type="text" name="name" value="{{ old('name') }}" class="split-input"
                            placeholder="Nama Lengkap" required autofocus>
                    </div>

                    <div class="split-input-group">
                        <input type="text" name="jabatan" value="{{ old('jabatan') }}" class="split-input"
                            placeholder="Jabatan (contoh: Kepala Dusun)" required>
                    </div>

                    <div class="split-input-group">
                        <input type="email" name="email" value="{{ old('email') }}" class="split-input"
                            placeholder="Alamat Email" required>
                    </div>

                    <div class="split-input-group">
                        <input type="password" name="password" class="split-input" placeholder="Buat Kata Sandi" required>
                    </div>

                    <div class="split-input-group">
                        <input type="password" name="password_confirmation" class="split-input"
                            placeholder="Konfirmasi Kata Sandi" required>
                    </div>

                    <button type="submit" class="btn split-login-btn mb-3">Daftar Akun</button>

                    <div class="text-center mt-3">
                        <span class="text-muted text-sm">Sudah punya akun?</span>
                        <a href="{{ route('login') }}" class="text-sm font-weight-bold"
                            style="color: var(--brand-primary);">Masuk</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection