@extends('layouts.app')
@section('title', 'Riwayat Surat')

@php
    function sortLinkSurat($column, $label, $sort, $direction) {
        $dir = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
        $icon = $sort === $column ? ($direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort';
        $q = array_merge(request()->query(), ['sort' => $column, 'direction' => $dir]);
        return '<a href="'.route('surat.index', $q).'" class="text-dark text-decoration-none">'.$label.' <i class="fas '.$icon.' text-muted small"></i></a>';
    }
@endphp

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title mb-0"><i class="fas fa-history mr-1"></i> Riwayat Surat</h3>
        <a href="{{ route('surat.create') }}" class="btn btn-sm btn-primary ml-auto"><i class="fas fa-plus"></i> Buat Surat</a>
    </div>
    <div class="card-body">
        <form method="GET" class="form-row align-items-end mb-3">
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Pencarian</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Nomor / Nama / NIK">
            </div>
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Jenis Surat</label>
                <select name="jenis_surat_id" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    @foreach ($jenisList as $j)
                        <option value="{{ $j->id }}" @selected(request('jenis_surat_id') == $j->id)>{{ $j->nama_surat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label class="small mb-1">Dari</label>
                <input type="date" name="dari" value="{{ request('dari') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 mb-2">
                <label class="small mb-1">Sampai</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 mb-2">
                <button class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('surat.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>{!! sortLinkSurat('nomor_surat', 'Nomor Surat', $sort, $direction) !!}</th>
                        <th>Jenis</th>
                        <th>Penduduk</th>
                        <th>{!! sortLinkSurat('tanggal_surat', 'Tanggal', $sort, $direction) !!}</th>
                        <th>Admin</th>
                        <th>Berkas</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($surats as $s)
                        <tr>
                            <td><a href="{{ route('surat.show', $s) }}">{{ $s->nomor_surat }}</a></td>
                            <td><span class="badge badge-secondary">{{ $s->jenisSurat->kode_surat ?? '-' }}</span></td>
                            <td>{{ $s->penduduk->nama_lengkap ?? '-' }}<div class="small text-muted">{{ $s->penduduk->nik ?? '' }}</div></td>
                            <td>{{ $s->tanggal_surat?->format('d/m/Y') }}</td>
                            <td>{{ $s->user->name ?? '-' }}</td>
                            <td>@if ($s->hasFile())<span class="badge badge-success">Ada</span>@else<span class="badge badge-warning">Draft</span>@endif</td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('surat.show', $s) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('surat.edit', $s) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('surat.download', $s) }}" class="btn btn-xs btn-success"><i class="fas fa-download"></i></a>
                                <form action="{{ route('surat.destroy', $s) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus surat {{ $s->nomor_surat }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada surat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">Total {{ $surats->total() }} surat</small>
            {{ $surats->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
