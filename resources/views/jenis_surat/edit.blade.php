@extends('layouts.app')
@section('title', 'Edit Jenis Surat')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="feature-card">
                <div class="feature-card-header">
                    <h3 class="feature-card-title">
                        <i class="fas fa-layer-group"></i> Edit Jenis Surat
                    </h3>
                    <span class="badge-modern badge-blue">{{ $jenisSurat->kode_surat }}</span>
                </div>
                <form action="{{ route('jenis-surat.update', $jenisSurat) }}" method="POST" enctype="multipart/form-data">
                    @method('PUT')
                    <div class="feature-card-body">
                        @include('jenis_surat._form')
                    </div>
                    <div class="feature-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Perbarui
                        </button>
                        <a href="{{ route('jenis-surat.index') }}" class="btn btn-reset">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="info-card">
                <h6><i class="fas fa-chart-bar mr-1"></i> Info</h6>
                <p class="mb-1">Digunakan oleh: <strong>{{ $jenisSurat->surats_count }} surat</strong></p>
                <p class="mb-0">Status:
                    @if ($jenisSurat->is_active)
                        <span class="badge-modern badge-green">Aktif</span>
                    @else
                        <span class="badge-modern badge-gray">Nonaktif</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
@endsection