@extends('layouts.app')
@section('title', 'Detail Surat')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">{{ $surat->nomor_surat }}</h3>
                <span class="badge badge-info float-right">{{ $surat->jenisSurat->nama_surat ?? '' }}</span>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th width="30%">Tanggal Surat</th><td>{{ $surat->tanggal_surat?->format('d F Y') }}</td></tr>
                    <tr><th>Penduduk</th><td>{{ $surat->penduduk->nama_lengkap ?? '-' }} ({{ $surat->penduduk->nik ?? '-' }})</td></tr>
                    <tr><th>Dibuat oleh</th><td>{{ $surat->user->name ?? '-' }}</td></tr>
                </table>
                <h6 class="text-primary"><i class="fas fa-file-alt mr-1"></i> Isi Surat</h6>
                <table class="table table-sm table-bordered">
                    @foreach ($surat->data_surat as $key => $value)
                        <tr><th width="30%">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $key)) }}</th><td>{{ is_array($value) ? implode(', ', $value) : $value }}</td></tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Tindakan</h3></div>
            <div class="card-body">
                <a href="{{ route('surat.edit', $surat) }}" class="btn btn-warning btn-block"><i class="fas fa-edit"></i> Sunting Isi</a>

                <form action="{{ route('surat.generate', $surat) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary btn-block"><i class="fas fa-file-word"></i> Simpan &amp; Buat Surat (.docx)</button>
                </form>

                @if ($surat->hasFile())
                    <hr>
                    <a href="{{ route('surat.download', $surat) }}" class="btn btn-success btn-block"><i class="fas fa-download"></i> Unduh .docx</a>
                    <a href="{{ route('surat.print', $surat) }}" class="btn btn-outline-primary btn-block"><i class="fas fa-print"></i> Cetak Ulang (regenerasi)</a>
                    <p class="small text-muted mt-2 mb-0">Berkas siap dibuka di Microsoft Word untuk dicetak.</p>
                @else
                    <p class="small text-muted mt-2">Berkas .docx belum dibuat. Klik tombol di atas untuk menghasilkannya.</p>
                @endif

                <hr>
                <form action="{{ route('surat.destroy', $surat) }}" method="POST" onsubmit="return confirm('Hapus surat ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-block"><i class="fas fa-trash"></i> Hapus Surat</button>
                </form>
            </div>
        </div>
    </div>
</div>
<a href="{{ route('surat.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Riwayat Surat</a>
@endsection
