@extends('layouts.app')
@section('title', 'Detail Surat')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="feature-card">
            <div class="feature-card-header">
                <h3 class="feature-card-title"><i class="fas fa-file-alt"></i> {{ $surat->nomor_surat }}</h3>
                <span class="badge-modern badge-blue">{{ $surat->jenisSurat->nama_surat ?? '' }}</span>
            </div>
            <div class="feature-card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <tr><th width="30%">Tanggal Surat</th><td>{{ $surat->tanggal_surat?->format('d F Y') }}</td></tr>
                        <tr><th>Penduduk</th><td>{{ $surat->penduduk->nama_lengkap ?? '-' }} ({{ $surat->penduduk->nik ?? '-' }})</td></tr>
                        <tr><th>Kop Surat</th><td>
                            @if ($surat->pakai_kop)
                                <span class="badge-modern badge-green">Dengan kop</span>
                            @else
                                <span class="badge-modern badge-gray">Tanpa kop</span>
                            @endif
                        </td></tr>
                        <tr><th>Dibuat oleh</th><td>{{ $surat->user->name ?? '-' }}</td></tr>
                    </table>
                </div>

                <div class="form-section-title mt-3"><i class="fas fa-file-alt"></i> Isi Surat</div>
                @php($reserved = ['penandatangan', 'jabatan_ttd', 'penandatangan_role'])
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        @foreach ($surat->data_surat as $key => $value)
                            @continue(in_array($key, $reserved))
                            <tr><th width="30%">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $key)) }}</th><td>{{ is_array($value) ? implode(', ', $value) : $value }}</td></tr>
                        @endforeach
                        <tr class="table-light">
                            <th width="30%"><i class="fas fa-user-tie mr-1"></i> Penandatangan</th>
                            <td>{{ $surat->data('penandatangan', '-') }} <span class="text-muted">({{ $surat->data('jabatan_ttd', 'Kepala Desa') }})</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="feature-card">
            <div class="feature-card-header">
                <h3 class="feature-card-title"><i class="fas fa-bolt"></i> Tindakan</h3>
            </div>
            <div class="feature-card-body">
                <a href="{{ route('surat.edit', $surat) }}" class="btn btn-warning btn-block mb-2"><i class="fas fa-pen"></i> Sunting Isi</a>

                <form action="{{ route('surat.generate', $surat) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary btn-block"><i class="fas fa-file-word"></i> Simpan &amp; Buat Surat (.docx)</button>
                </form>

                @if ($surat->hasFile())
                    <hr>
                    <a href="{{ route('surat.print', $surat) }}" target="_blank" class="btn btn-primary btn-block mb-2"><i class="fas fa-print"></i> Print (cetak langsung)</a>
                    <a href="{{ route('surat.download', $surat) }}" class="btn btn-success btn-block"><i class="fas fa-file-word"></i> Unduh .docx</a>
                    <p class="small text-muted mt-2 mb-0">Print membuka dialog cetak langsung di browser. .docx untuk diedit di Microsoft Word.</p>
                @else
                    <p class="small text-muted mt-2">Berkas belum dibuat. Klik tombol di atas untuk menghasilkannya.</p>
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
<a href="{{ route('surat.index') }}" class="btn btn-reset"><i class="fas fa-arrow-left"></i> Riwayat Surat</a>
@endsection
