@extends('layouts.app')
@section('title', 'Riwayat Surat')

@php
    function sortLinkSurat($column, $label, $sort, $direction)
    {
        $dir = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
        $icon = $sort === $column ? ($direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort';
        $q = array_merge(request()->query(), ['sort' => $column, 'direction' => $dir]);
        return '<a href="' . route('surat.index', $q) . '" class="text-dark text-decoration-none font-weight-bold">' . $label . ' <i class="fas ' . $icon . ' text-muted small"></i></a>';
    }
@endphp

@section('content')
    <div class="feature-card">
        {{-- Header --}}
        <div class="feature-card-header">
            <h3 class="feature-card-title">
                <i class="fas fa-envelope-open-text"></i> Riwayat Surat
            </h3>
            <a href="{{ route('surat.create') }}" class="header-btn btn-add">
                <i class="fas fa-plus"></i> Buat Surat
            </a>
        </div>

        <div class="feature-card-body">
            {{-- Filter Bar --}}
            <form method="GET">
                <div class="filter-bar">
                    <div class="filter-group">
                        <label>Pencarian</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                            placeholder="Nomor / Nama / NIK">
                    </div>
                    <div class="filter-group">
                        <label>Jenis Surat</label>
                        <select name="jenis_surat_id" class="form-control">
                            <option value="">Semua</option>
                            @foreach ($jenisList as $j)
                                <option value="{{ $j->id }}" @selected(request('jenis_surat_id') == $j->id)>{{ $j->nama_surat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group" style="max-width:155px;">
                        <label>Dari Tanggal</label>
                        <input type="date" name="dari" value="{{ request('dari') }}" class="form-control">
                    </div>
                    <div class="filter-group" style="max-width:155px;">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-filter"><i class="fas fa-filter"></i> Filter</button>
                        <a href="{{ route('surat.index') }}" class="btn btn-reset">Reset</a>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
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
                                <td>
                                    <a href="{{ route('surat.show', $s) }}" style="font-weight:700;color:var(--brand-primary);">
                                        {{ $s->nomor_surat }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge-modern badge-blue">{{ $s->jenisSurat->kode_surat ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="name-primary">{{ $s->penduduk->nama_lengkap ?? '-' }}</span>
                                    @if ($s->penduduk->nik ?? false)
                                        <div class="subtext">{{ $s->penduduk->nik }}</div>
                                    @endif
                                </td>
                                <td style="white-space:nowrap; font-weight:600;">
                                    {{ $s->tanggal_surat?->format('d/m/Y') }}
                                </td>
                                <td>{{ $s->user->name ?? '-' }}</td>
                                <td>
                                    @if ($s->hasFile())
                                        <span class="badge-modern badge-green">
                                            <span class="status-dot dot-green"></span> Ada
                                        </span>
                                    @else
                                        <span class="badge-modern badge-gold">
                                            <span class="status-dot dot-gold"></span> Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right text-nowrap">
                                    <a href="{{ route('surat.show', $s) }}" class="action-btn btn-view" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('surat.edit', $s) }}" class="action-btn btn-edit" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="{{ route('surat.print', $s) }}" target="_blank" class="action-btn btn-view"
                                        title="Print">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="{{ route('surat.download', $s) }}" class="action-btn btn-download"
                                        title="Unduh .docx">
                                        <i class="fas fa-file-word"></i>
                                    </a>
                                    <form action="{{ route('surat.destroy', $s) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus surat {{ $s->nomor_surat }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn btn-delete" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-state">
                                <td colspan="7">
                                    <i class="fas fa-envelope-open"></i>
                                    <div>Belum ada surat yang dibuat.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="pagination-info">Total {{ $surats->total() }} surat</span>
                {{ $surats->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection