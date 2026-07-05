@extends('layouts.app')
@section('title', 'Jenis Surat')

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title mb-0"><i class="fas fa-layer-group mr-1"></i> Daftar Jenis Surat</h3>
        <a href="{{ route('jenis-surat.create') }}" class="btn btn-sm btn-primary ml-auto"><i class="fas fa-plus"></i> Tambah</a>
    </div>
    <div class="card-body">
        <form method="GET" class="form-inline mb-3">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm mr-2" placeholder="Cari nama / kode">
            <button class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr><th>Kode</th><th>Nama Surat</th><th>Field Tambahan</th><th>Template</th><th>Dipakai</th><th>Status</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($jenisSurats as $js)
                        <tr>
                            <td><span class="badge badge-secondary">{{ $js->kode_surat }}</span></td>
                            <td>{{ $js->nama_surat }}<div class="small text-muted">{{ Str::limit($js->deskripsi, 60) }}</div></td>
                            <td>{{ count($js->fields ?? []) }} field</td>
                            <td>
                                @if ($js->hasTemplate())
                                    <a href="{{ route('jenis-surat.template', $js) }}" class="badge badge-success"><i class="fas fa-file-word"></i> Ada</a>
                                @else
                                    <span class="badge badge-warning">Belum ada</span>
                                @endif
                            </td>
                            <td>{{ $js->surats_count }}</td>
                            <td>@if ($js->is_active)<span class="badge badge-primary">Aktif</span>@else<span class="badge badge-secondary">Nonaktif</span>@endif</td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('jenis-surat.edit', $js) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('jenis-surat.destroy', $js) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus jenis surat ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada jenis surat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $jenisSurats->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
