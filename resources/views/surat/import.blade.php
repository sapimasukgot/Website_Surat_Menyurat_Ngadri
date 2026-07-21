@extends('layouts.app')
@section('title', 'Import / Restore Riwayat Surat')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-file-import mr-1"></i> Restore Riwayat Surat dari Backup</h3></div>
            <form action="{{ route('surat.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Berkas backup (.xlsx / .xls, maks 10 MB)</label>
                        <div class="custom-file">
                            <input type="file" name="file" id="file" class="custom-file-input @error('file') is-invalid @enderror" accept=".xlsx,.xls" required>
                            <label class="custom-file-label" for="file">Pilih berkas...</label>
                            @error('file') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="alert alert-info small mb-2">
                        <strong>Cara pakai:</strong> gunakan berkas hasil tombol <b>Export Excel</b> pada halaman
                        Riwayat Surat. Surat dikenali dari <b>Nomor Surat</b> — nomor baru <b>ditambahkan</b>,
                        nomor yang sudah ada <b>diperbarui</b> (aman diimpor berulang, tanpa duplikat).
                        Jenis surat dicocokkan lewat <b>Kode Surat</b> dan pemohon lewat <b>NIK</b>.
                    </div>
                    <div class="alert alert-warning small mb-0">
                        <strong>Urutan restore di komputer cadangan:</strong>
                        1) Import <b>Data Penduduk</b> dahulu, 2) pastikan <b>Jenis Surat</b> tersedia
                        (bawaan aplikasi/seeder), 3) baru import riwayat surat ini.
                        Berkas Word tidak perlu dibackup — otomatis dibuat ulang saat surat diunduh/dicetak.
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary"><i class="fas fa-upload"></i> Import</button>
                    <a href="{{ route('surat.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        @if ($lastLog)
            <div class="card card-outline card-secondary">
                <div class="card-header"><h3 class="card-title">Hasil Import Terakhir</h3></div>
                <div class="card-body">
                    <p class="mb-1"><b>Berkas:</b> {{ $lastLog->file_name }}</p>
                    <p class="mb-2 text-muted small">{{ $lastLog->created_at->format('d/m/Y H:i') }} oleh {{ $lastLog->user->name ?? '-' }}</p>
                    <div class="row text-center">
                        <div class="col"><span class="badge badge-success p-2">Baru: {{ $lastLog->inserted_count }}</span></div>
                        <div class="col"><span class="badge badge-info p-2">Diperbarui: {{ $lastLog->updated_count }}</span></div>
                        <div class="col"><span class="badge badge-danger p-2">Gagal: {{ $lastLog->failed_count }}</span></div>
                    </div>
                    @if (!empty($lastLog->errors))
                        <hr>
                        <h6>Detail Error</h6>
                        <div class="table-responsive" style="max-height:240px;overflow:auto">
                            <table class="table table-sm">
                                <thead><tr><th>Baris</th><th>Nomor Surat</th><th>Pesan</th></tr></thead>
                                <tbody>
                                    @foreach ($lastLog->errors as $err)
                                        <tr><td>{{ $err['baris'] }}</td><td>{{ $err['nik'] }}</td><td class="small">{{ $err['pesan'] }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('file').addEventListener('change', function (e) {
        e.target.nextElementSibling.textContent = e.target.files[0]?.name || 'Pilih berkas...';
    });
</script>
@endpush
@endsection
