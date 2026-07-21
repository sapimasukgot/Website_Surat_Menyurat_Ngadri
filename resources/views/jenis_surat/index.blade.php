@extends('layouts.app')
@section('title', 'Jenis Surat')

@section('content')
    <div class="feature-card">
        {{-- Header --}}
        <div class="feature-card-header">
            <h3 class="feature-card-title">
                <i class="fas fa-layer-group"></i> Daftar Jenis Surat
            </h3>
            <a href="{{ route('jenis-surat.create') }}" class="header-btn btn-add">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>

        <div class="feature-card-body">
            {{-- Filter Bar --}}
            <form method="GET">
                <div class="filter-bar">
                    <div class="filter-group">
                        <label>Cari Jenis Surat</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                            placeholder="Nama surat / kode...">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-filter"><i class="fas fa-search"></i> Cari</button>
                        <a href="{{ route('jenis-surat.index') }}" class="btn btn-reset">Reset</a>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Surat</th>
                            <th>Field Tambahan</th>
                            <th>Template</th>
                            <th>Dipakai</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jenisSurats as $js)
                            <tr>
                                <td>
                                    <span class="badge-modern badge-blue">{{ $js->kode_surat }}</span>
                                </td>
                                <td>
                                    <span class="name-primary">{{ $js->nama_surat }}</span>
                                    @if ($js->deskripsi)
                                        <div class="subtext">{{ Str::limit($js->deskripsi, 60) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-modern badge-gray">
                                        <i class="fas fa-list-ul"></i> {{ count($js->fields ?? []) }} field
                                    </span>
                                </td>
                                <td>
                                    @if ($js->hasTemplate())
                                        <a href="{{ route('jenis-surat.template', $js) }}" class="badge-modern badge-green"
                                            style="text-decoration:none;">
                                            <i class="fas fa-file-word"></i> Ada
                                        </a>
                                    @else
                                        <span class="badge-modern badge-gold">
                                            <i class="fas fa-clock"></i> Belum ada
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-weight-600" style="color:#2D3748;">{{ $js->surats_count }}</span>
                                </td>
                                <td>
                                    @if ($js->is_active)
                                        <span class="badge-modern badge-green"><span class="status-dot dot-green"></span>
                                            Aktif</span>
                                    @else
                                        <span class="badge-modern badge-gray"><span class="status-dot"
                                                style="background:#CBD5E0;"></span> Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-right text-nowrap">
                                    <a href="{{ route('jenis-surat.edit', $js) }}" class="action-btn btn-edit" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('jenis-surat.destroy', $js) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus jenis surat ini?')">
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
                                    <i class="fas fa-folder-open"></i>
                                    <div>Belum ada jenis surat yang dibuat.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $jenisSurats->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection