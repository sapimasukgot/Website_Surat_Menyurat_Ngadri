@extends('layouts.app')
@section('title', 'Data Penduduk')

@php
    function sortLink($column, $label, $sort, $direction) {
        $dir = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
        $icon = $sort === $column ? ($direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort';
        $q = array_merge(request()->query(), ['sort' => $column, 'direction' => $dir]);
        return '<a href="'.route('penduduk.index', $q).'" class="text-dark text-decoration-none">'.$label.' <i class="fas '.$icon.' text-muted small"></i></a>';
    }
@endphp

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header d-flex flex-wrap align-items-center">
        <h3 class="card-title mb-0"><i class="fas fa-users mr-1"></i> Daftar Penduduk</h3>
        <div class="ml-auto">
            <a href="{{ route('penduduk.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Tambah</a>
            <a href="{{ route('penduduk.import.form') }}" class="btn btn-sm btn-success"><i class="fas fa-file-import"></i> Import</a>
            <a href="{{ route('penduduk.export') }}" class="btn btn-sm btn-secondary"><i class="fas fa-file-excel"></i> Export</a>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" class="form-row align-items-end mb-3">
            <div class="col-md-4 mb-2">
                <label class="small mb-1">Pencarian</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="NIK / No KK / Nama">
            </div>
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Dusun</label>
                <select name="dusun" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    @foreach ($dusunList as $d)
                        <option value="{{ $d }}" @selected(request('dusun') === $d)>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label class="small mb-1">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <option value="L" @selected(request('jenis_kelamin')==='L')>Laki-laki</option>
                    <option value="P" @selected(request('jenis_kelamin')==='P')>Perempuan</option>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <button class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Cari</button>
                <a href="{{ route('penduduk.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>{!! sortLink('nik', 'NIK', $sort, $direction) !!}</th>
                        <th>{!! sortLink('nama_lengkap', 'Nama Lengkap', $sort, $direction) !!}</th>
                        <th>L/P</th>
                        <th>RT/RW</th>
                        <th>{!! sortLink('dusun', 'Dusun', $sort, $direction) !!}</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penduduks as $p)
                        <tr>
                            <td><code>{{ $p->nik }}</code></td>
                            <td>{{ $p->nama_lengkap }}</td>
                            <td>{{ $p->jenis_kelamin }}</td>
                            <td>{{ $p->rt }}/{{ $p->rw }}</td>
                            <td>{{ $p->dusun }}</td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('penduduk.show', $p) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('penduduk.edit', $p) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('penduduk.destroy', $p) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus data {{ $p->nama_lengkap }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data penduduk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">Menampilkan {{ $penduduks->firstItem() ?? 0 }}-{{ $penduduks->lastItem() ?? 0 }} dari {{ $penduduks->total() }} data</small>
            {{ $penduduks->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
