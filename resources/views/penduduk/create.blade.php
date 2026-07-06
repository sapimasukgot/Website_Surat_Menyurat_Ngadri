@extends('layouts.app')
@section('title', 'Tambah Penduduk')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="feature-card">
                <div class="feature-card-header">
                    <h3 class="feature-card-title">
                        <i class="fas fa-user-plus"></i> Tambah Data Penduduk
                    </h3>
                </div>
                <form action="{{ route('penduduk.store') }}" method="POST">
                    <div class="feature-card-body">
                        @include('penduduk._form')
                    </div>
                    <div class="feature-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Data
                        </button>
                        <a href="{{ route('penduduk.index') }}" class="btn btn-reset">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="info-card">
                <h6><i class="fas fa-info-circle mr-1"></i> Panduan</h6>
                <p>Field bertanda <strong style="color:var(--brand-danger)">*</strong> wajib diisi. NIK dan No. KK harus
                    terdiri dari 16 digit angka.</p>
            </div>
        </div>
    </div>
@endsection