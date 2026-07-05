@extends('layouts.app')
@section('title', 'Sunting Surat')

@php($longFields = ['alamat', 'keterangan_tambahan', 'alamat_usaha'])

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Sunting Isi Surat</h3>
                <span class="badge badge-info float-right">{{ $surat->jenisSurat->nama_surat ?? '' }}</span>
            </div>
            <form action="{{ route('surat.update', $surat) }}" method="POST">
                @csrf @method('PUT')
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <label>Nomor Surat</label>
                            <input type="text" name="nomor_surat" class="form-control @error('nomor_surat') is-invalid @enderror"
                                   value="{{ old('nomor_surat', $surat->nomor_surat) }}" required>
                            @error('nomor_surat') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group col-md-5">
                            <label>Tanggal Surat</label>
                            <input type="date" name="tanggal_surat" class="form-control"
                                   value="{{ old('tanggal_surat', $surat->tanggal_surat?->format('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <h6 class="text-primary mt-2"><i class="fas fa-database mr-1"></i> Isi Surat (dapat disunting)</h6>
                    <div class="form-row">
                        @foreach ($surat->data_surat as $key => $value)
                            <div class="form-group {{ in_array($key, $longFields) ? 'col-12' : 'col-md-6' }}">
                                <label class="mb-1">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $key)) }}</label>
                                @if (in_array($key, $longFields))
                                    <textarea name="data[{{ $key }}]" rows="2" class="form-control">{{ old('data.'.$key, $value) }}</textarea>
                                @else
                                    <input type="text" name="data[{{ $key }}]" class="form-control" value="{{ old('data.'.$key, $value) }}">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group">
                        <label>Keterangan (internal)</label>
                        <textarea name="keterangan" rows="2" class="form-control">{{ old('keterangan', $surat->keterangan) }}</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    <a href="{{ route('surat.show', $surat) }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="alert alert-info">
            <h6><i class="fas fa-info-circle mr-1"></i> Catatan</h6>
            <p class="small mb-0">Isi yang Anda simpan menjadi <b>isi final</b> surat. Bila template diubah di kemudian hari, surat ini tetap memakai data yang tersimpan di sini.</p>
        </div>
        <div class="card">
            <div class="card-body">
                <p class="mb-1"><b>Penduduk:</b> {{ $surat->penduduk->nama_lengkap ?? '-' }}</p>
                <p class="mb-1"><b>NIK:</b> {{ $surat->penduduk->nik ?? '-' }}</p>
                <p class="mb-0"><b>Dibuat oleh:</b> {{ $surat->user->name ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
