@extends('layouts.guest')
@section('title', 'Masuk')
@section('body_class', 'login-page-split')

@section('content')
    <div class="login-split-container">
        <!-- Left Column: Branding Showcase -->
        <div class="login-split-brand">
            <div class="brand-showcase">
                <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('desa.nama') }}" class="brand-showcase-logo">
                <h1>Halo {{ config('desa.nama') }}!</h1>
                <p>Sistem Administrasi Surat Menyurat Terpadu. Mempermudah pelayanan surat penduduk secara efisien, cepat,
                    dan transparan.</p>
            </div>
            <div class="brand-showcase-footer">
                &copy; 2026 {{ config('desa.nama') }}. All rights reserved.
            </div>
        </div>

        <!-- Right Column: Login Form -->
        <div class="login-split-form">
            <div class="login-form-inner">
                <div class="login-form-logo-section">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="login-form-logo">
                    <span class="login-form-title">Surat Desa</span>
                </div>

                <h2 class="login-welcome-title">Selamat Datang!</h2>
                <p class="login-welcome-subtitle">Masukkan kredensial Anda untuk masuk ke sistem.</p>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 mb-4" style="border-radius: 8px;">
                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="split-input-group">
                        <input type="email" name="email" value="{{ old('email') }}" class="split-input"
                            placeholder="Masukkan Email" required autofocus>
                    </div>

                    <div class="split-input-group">
                        <input type="password" name="password" class="split-input" placeholder="Masukkan Kata Sandi"
                            required>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                            <label class="custom-control-label text-muted text-sm"
                                style="font-weight: 500; cursor: pointer;" for="remember">Ingat saya</label>
                        </div>
                    </div>

                    <button type="submit" class="btn split-login-btn">Masuk Sistem</button>
                </form>
            </div>
        </div>
    </div>
@endsection