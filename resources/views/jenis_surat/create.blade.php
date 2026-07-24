@extends('layouts.app')
@section('title', 'Tambah Jenis Surat')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="feature-card">
                <div class="feature-card-header">
                    <h3 class="feature-card-title">
                        <i class="fas fa-layer-group"></i> Tambah Jenis Surat
                    </h3>
                </div>
                <form action="{{ route('jenis-surat.store') }}" method="POST" enctype="multipart/form-data">
                    <div class="feature-card-body">
                        @include('jenis_surat._form')
                    </div>
                    <div class="feature-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <a href="{{ route('jenis-surat.index') }}" class="btn btn-reset">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="info-card">
                <h6><i class="fas fa-lightbulb mr-1"></i> Tips Placeholder</h6>
                <p>Di template Word, gunakan placeholder seperti: <code>${nomor_surat}</code>, <code>${nama}</code>,
                    <code>${nik}</code>, <code>${alamat}</code>, <code>${tanggal}</code>.</p>
            </div>
        </div>
    </div>
@endsection