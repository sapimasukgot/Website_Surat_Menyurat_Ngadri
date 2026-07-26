@extends('layouts.app')
@section('title', 'Detail Penduduk')

@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="feature-card">
            <div class="feature-card-header">
                <h3 class="feature-card-title"><i class="fas fa-user"></i> {{ $penduduk->nama_lengkap }}</h3>
                <a href="{{ route('penduduk.edit', $penduduk) }}" class="header-btn btn-add" title="Edit data penduduk">
                    <i class="fas fa-pen"></i> Edit
                </a>
            </div>
            <div class="feature-card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <tr><th width="35%">NIK</th><td>{{ $penduduk->nik }}</td></tr>
                        <tr><th>Nomor KK</th><td>{{ $penduduk->no_kk }}</td></tr>
                        <tr><th>Tempat, Tgl Lahir</th><td>{{ $penduduk->tempat_lahir }}, {{ $penduduk->tanggal_lahir?->format('d-m-Y') }}</td></tr>
                        <tr><th>Jenis Kelamin</th><td>{{ $penduduk->jenis_kelamin_label }}</td></tr>
                        <tr><th>Golongan Darah</th><td>{{ $penduduk->golongan_darah ?: '-' }}</td></tr>
                        <tr><th>Agama</th><td>{{ $penduduk->agama }}</td></tr>
                        <tr><th>Pendidikan</th><td>{{ $penduduk->pendidikan }}</td></tr>
                        <tr><th>Pekerjaan</th><td>{{ $penduduk->pekerjaan }}</td></tr>
                        <tr><th>Status Kawin</th><td>{{ $penduduk->status_kawin }}</td></tr>
                        <tr><th>Status Hubungan Keluarga</th><td>{{ $penduduk->status_hubungan ?: '-' }}</td></tr>
                        <tr><th>Nama Ayah</th><td>{{ $penduduk->nama_ayah ?: '-' }}</td></tr>
                        <tr><th>Nama Ibu</th><td>{{ $penduduk->nama_ibu ?: '-' }}</td></tr>
                        <tr><th>Alamat</th><td>{{ $penduduk->alamat }} RT {{ $penduduk->rt }}/RW {{ $penduduk->rw }}, Dusun {{ $penduduk->dusun }}</td></tr>
                        <tr><th>No HP</th><td>{{ $penduduk->no_hp }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="feature-card">
            <div class="feature-card-header">
                <h3 class="feature-card-title"><i class="fas fa-history"></i> Riwayat Surat</h3>
            </div>
            <div class="feature-card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Nomor</th><th>Jenis</th><th>Tgl</th></tr></thead>
                        <tbody>
                            @forelse ($penduduk->surats as $s)
                                <tr><td><a href="{{ route('surat.show', $s) }}">{{ $s->nomor_surat }}</a></td>
                                    <td>{{ $s->jenisSurat->kode_surat ?? '-' }}</td>
                                    <td>{{ $s->tanggal_surat?->format('d/m/y') }}</td></tr>
                            @empty
                                <tr class="empty-state">
                                    <td colspan="3">
                                        <i class="fas fa-folder-open"></i>
                                        <div>Belum ada surat.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<a href="{{ route('penduduk.index') }}" class="btn btn-reset"><i class="fas fa-arrow-left"></i> Kembali</a>
@endsection
