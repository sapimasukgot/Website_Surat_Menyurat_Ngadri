@extends('layouts.app')
@section('title', 'Edit Penduduk')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="feature-card">
                <div class="feature-card-header">
                    <h3 class="feature-card-title">
                        <i class="fas fa-user-edit"></i> Edit Data Penduduk
                    </h3>
                    <span class="nik-code">{{ $penduduk->nik }}</span>
                </div>
                <form action="{{ route('penduduk.update', $penduduk) }}" method="POST">
                    @method('PUT')
                    <div class="feature-card-body">
                        @include('penduduk._form')
                    </div>
                    <div class="feature-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Perbarui Data
                        </button>
                        <a href="{{ route('penduduk.index') }}" class="btn btn-reset">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="info-card">
                <h6><i class="fas fa-user mr-1"></i> {{ $penduduk->nama_lengkap }}</h6>
                <p class="mb-1">NIK: <strong>{{ $penduduk->nik }}</strong></p>
                <p class="mb-0">Dusun: <strong>{{ $penduduk->dusun ?? '-' }}</strong></p>
            </div>
        </div>
    </div>
@endsection