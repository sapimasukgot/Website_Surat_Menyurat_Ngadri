@extends('layouts.app')
@section('title', 'Sunting Surat')

@php($longFields = ['alamat', 'keterangan_tambahan', 'alamat_usaha'])
@php($reserved = ['penandatangan', 'jabatan_ttd', 'penandatangan_role'])
@php($currentRole = $surat->data_surat['penandatangan_role'] ?? 'kepala_desa')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="feature-card">
                <div class="feature-card-header">
                    <h3 class="feature-card-title">
                        <i class="fas fa-file-signature"></i> Sunting Isi Surat
                    </h3>
                    <span class="badge-modern badge-blue">{{ $surat->jenisSurat->nama_surat ?? '' }}</span>
                </div>
                <form action="{{ route('surat.update', $surat) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="feature-card-body">
                        <div class="form-section-title"><i class="fas fa-hashtag"></i> Identitas Surat</div>
                        <div class="form-row">
                            <div class="form-group col-md-7">
                                <label>Nomor Surat</label>
                                <input type="text" name="nomor_surat"
                                    class="form-control @error('nomor_surat') is-invalid @enderror"
                                    value="{{ old('nomor_surat', $surat->nomor_surat) }}" required>
                                @error('nomor_surat') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-5">
                                <label>Tanggal Surat</label>
                                <input type="date" name="tanggal_surat" class="form-control"
                                    value="{{ old('tanggal_surat', $surat->tanggal_surat?->format('Y-m-d')) }}" required>
                            </div>
                        </div>

                        @if (count($surat->data_surat))
                            <div class="dynamic-fields-box">
                                <h6><i class="fas fa-database mr-1"></i>Isi Surat (dapat disunting)</h6>
                                <div class="form-row">
                                    @foreach ($surat->data_surat as $key => $value)
                                        @continue(in_array($key, $reserved))
                                        <div class="form-group {{ in_array($key, $longFields) ? 'col-12' : 'col-md-6' }}">
                                            <label style="font-size:0.84rem;font-weight:700;color:#4A5568;">
                                                {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $key)) }}
                                            </label>
                                            @if (in_array($key, $longFields))
                                                <textarea name="data[{{ $key }}]" rows="2"
                                                    class="form-control">{{ old('data.' . $key, $value) }}</textarea>
                                            @else
                                                <input type="text" name="data[{{ $key }}]" class="form-control"
                                                    value="{{ old('data.' . $key, $value) }}">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="form-section-title"><i class="fas fa-user-tie"></i> Penandatangan</div>
                        <div class="form-group">
                            <label>Ditandatangani oleh <span class="text-danger">*</span></label>
                            <select name="penandatangan_role" class="form-control">
                                <option value="kepala_desa" @selected(old('penandatangan_role', $currentRole) === 'kepala_desa')>
                                    Kepala Desa — {{ $penandatanganList['kepala_desa'] ?: 'belum diatur' }}
                                </option>
                                <option value="sekretaris_desa" @selected(old('penandatangan_role', $currentRole) === 'sekretaris_desa')>
                                    Sekretaris Desa — {{ $penandatanganList['sekretaris_desa'] ?: 'belum diatur' }}
                                </option>
                            </select>
                            <small class="text-muted" style="font-size:0.8rem;">Nama penandatangan mengikuti pengaturan
                                perangkat desa terbaru saat disimpan.</small>
                        </div>

                        <div class="form-section-title"><i class="fas fa-sticky-note"></i> Keterangan</div>
                        <div class="form-group">
                            <label>Keterangan Internal</label>
                            <textarea name="keterangan" rows="2" class="form-control"
                                placeholder="Catatan internal...">{{ old('keterangan', $surat->keterangan) }}</textarea>
                        </div>
                    </div>
                    <div class="feature-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('surat.show', $surat) }}" class="btn btn-reset">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="info-card mb-3">
                <h6><i class="fas fa-info-circle mr-1"></i> Catatan</h6>
                <p>Isi yang Anda simpan menjadi <strong>isi final</strong> surat. Bila template diubah di kemudian hari,
                    surat ini tetap memakai data yang tersimpan di sini.</p>
            </div>
            <div class="feature-card" style="margin-bottom:0;">
                <div class="feature-card-header">
                    <h3 class="feature-card-title"><i class="fas fa-user"></i> Data Pemohon</h3>
                </div>
                <div class="feature-card-body" style="padding: 1rem 1.5rem;">
                    <p class="mb-1" style="font-size:0.9rem;"><span class="text-muted">Nama:</span>
                        <strong>{{ $surat->penduduk->nama_lengkap ?? '-' }}</strong></p>
                    <p class="mb-1" style="font-size:0.9rem;"><span class="text-muted">NIK:</span> <span
                            class="nik-code">{{ $surat->penduduk->nik ?? '-' }}</span></p>
                    <p class="mb-0" style="font-size:0.9rem;"><span class="text-muted">Admin:</span>
                        {{ $surat->user->name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection