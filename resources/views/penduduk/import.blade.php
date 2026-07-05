@extends('layouts.app')
@section('title', 'Import Data Penduduk')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-file-import mr-1"></i> Unggah Berkas Excel</h3></div>
            <form action="{{ route('penduduk.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Berkas (.xlsx / .xls, maks 5 MB)</label>
                        <div class="custom-file">
                            <input type="file" name="file" id="file" class="custom-file-input @error('file') is-invalid @enderror" accept=".xlsx,.xls" required>
                            <label class="custom-file-label" for="file">Pilih berkas...</label>
                            @error('file') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="alert alert-info small mb-0">
                        <strong>Aturan import:</strong> NIK baru akan <b>ditambahkan</b>, NIK yang sudah ada akan <b>diperbarui</b> (tanpa duplikat).
                        Kolom yang diharapkan: <code>NIK, No KK, Nama Lengkap, Tempat Lahir, Tanggal Lahir, Jenis Kelamin, Agama, Pendidikan, Pekerjaan, Status Kawin, Alamat, RT, RW, Dusun, No HP</code>.
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary"><i class="fas fa-upload"></i> Import</button>
                    <a href="{{ route('penduduk.index') }}" class="btn btn-outline-secondary">Kembali</a>
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
                                <thead><tr><th>Baris</th><th>NIK</th><th>Pesan</th></tr></thead>
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
        const name = e.target.files[0]?.name || 'Pilih berkas...';
        e.target.nextElementSibling.textContent = name;
    });
</script>
@endpush
@endsection
