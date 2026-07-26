@extends('layouts.app')
@section('title', 'Data Penduduk')

@php
    function sortLink($column, $label, $sort, $direction)
    {
        $dir = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
        $icon = $sort === $column ? ($direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort';
        $q = array_merge(request()->query(), ['sort' => $column, 'direction' => $dir]);
        return '<a href="' . route('penduduk.index', $q) . '" class="text-dark text-decoration-none font-weight-bold">' . $label . ' <i class="fas ' . $icon . ' text-muted small"></i></a>';
    }
@endphp

@section('content')
    <div class="feature-card">
        {{-- Header --}}
        <div class="feature-card-header">
            <h3 class="feature-card-title">
                <i class="fas fa-users"></i> Daftar Penduduk
            </h3>
            <div class="d-flex align-items-center gap-2" style="gap: 0.5rem;">
                <a href="{{ route('penduduk.create') }}" class="header-btn btn-add">
                    <i class="fas fa-plus"></i> Tambah
                </a>
                <a href="{{ route('penduduk.import.form') }}" class="header-btn btn-import">
                    <i class="fas fa-file-import"></i> Import
                </a>
                <a href="{{ route('penduduk.export') }}" class="header-btn btn-export">
                    <i class="fas fa-file-excel"></i> Export
                </a>
                <a href="{{ route('penduduk.export.adminduk') }}" class="header-btn btn-export"
                    title="Rekap jumlah penduduk per kategori & per umur (format laporan semesteran kantor desa)">
                    <i class="fas fa-chart-bar"></i> Laporan Adminduk
                </a>
            </div>
        </div>

        <div class="feature-card-body">
            {{-- Filter Bar --}}
            <form method="GET">
                <div class="filter-bar">
                    <div class="filter-group">
                        <label>Pencarian</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                            placeholder="NIK / No KK / Nama">
                    </div>
                    <div class="filter-group">
                        <label>Dusun</label>
                        <select name="dusun" class="form-control">
                            <option value="">Semua</option>
                            @foreach ($dusunList as $d)
                                <option value="{{ $d }}" @selected(request('dusun') === $d)>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group" style="max-width:170px">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control">
                            <option value="">Semua</option>
                            <option value="L" @selected(request('jenis_kelamin') === 'L')>Laki-laki</option>
                            <option value="P" @selected(request('jenis_kelamin') === 'P')>Perempuan</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-filter"><i class="fas fa-search"></i> Cari</button>
                        <a href="{{ route('penduduk.index') }}" class="btn btn-reset">Reset</a>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>{!! sortLink('nik', 'NIK', $sort, $direction) !!}</th>
                            <th>{!! sortLink('nama_lengkap', 'Nama Lengkap', $sort, $direction) !!}</th>
                            <th>Kelamin</th>
                            <th>RT/RW</th>
                            <th>{!! sortLink('dusun', 'Dusun', $sort, $direction) !!}</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penduduks as $p)
                            <tr>
                                <td><span class="nik-code">{{ $p->nik }}</span></td>
                                <td>
                                    <span class="name-primary">{{ $p->nama_lengkap }}</span>
                                </td>
                                <td>
                                    @if ($p->jenis_kelamin === 'L')
                                        <span class="badge-modern badge-blue"><i class="fas fa-mars"></i> L</span>
                                    @else
                                        <span class="badge-modern badge-red"><i class="fas fa-venus"></i> P</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-weight:600;color:#4A5568;">{{ $p->rt }}/{{ $p->rw }}</span>
                                </td>
                                <td>{{ $p->dusun }}</td>
                                <td class="text-right text-nowrap">
                                    <a href="{{ route('penduduk.show', $p) }}" class="action-btn btn-view" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('penduduk.edit', $p) }}" class="action-btn btn-edit" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('penduduk.destroy', $p) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus data {{ $p->nama_lengkap }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn btn-delete" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-state">
                                <td colspan="6">
                                    <i class="fas fa-users-slash"></i>
                                    <div>Tidak ada data penduduk ditemukan.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="pagination-info">
                    Menampilkan {{ $penduduks->firstItem() ?? 0 }}–{{ $penduduks->lastItem() ?? 0 }} dari
                    {{ $penduduks->total() }} data
                </span>
                {{ $penduduks->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection