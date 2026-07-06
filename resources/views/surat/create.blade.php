@extends('layouts.app')
@section('title', 'Buat Surat')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="feature-card">
                <div class="feature-card-header">
                    <h3 class="feature-card-title">
                        <i class="fas fa-file-signature"></i> Buat Surat Baru
                    </h3>
                </div>
                <form action="{{ route('surat.store') }}" method="POST">
                    @csrf
                    <div class="feature-card-body">
                        <div class="form-section-title"><i class="fas fa-cog"></i> Konfigurasi Surat</div>
                        <div class="form-row">
                            <div class="form-group col-md-7">
                                <label>Jenis Surat <span class="text-danger">*</span></label>
                                <select name="jenis_surat_id" id="jenis_select"
                                    class="form-control @error('jenis_surat_id') is-invalid @enderror" required>
                                    <option value="">— Pilih Jenis Surat —</option>
                                    @foreach ($jenisList as $j)
                                        <option value="{{ $j->id }}" @selected(old('jenis_surat_id') == $j->id)>
                                            {{ $j->nama_surat }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_surat_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-5">
                                <label>Tanggal Surat <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_surat" class="form-control"
                                    value="{{ old('tanggal_surat', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <div class="form-section-title mt-1"><i class="fas fa-user"></i> Penduduk Pemohon</div>
                        <div class="form-group">
                            <label>Penduduk <span class="text-danger">*</span></label>
                            <select name="penduduk_id"
                                class="form-control tom-select @error('penduduk_id') is-invalid @enderror"
                                data-placeholder="Cari NIK / Nama..." required>
                                <option value="">— Pilih Penduduk —</option>
                                @foreach ($penduduks as $p)
                                    <option value="{{ $p->id }}" @selected(old('penduduk_id') == $p->id)>{{ $p->nik }} —
                                        {{ $p->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            @error('penduduk_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            <small class="text-muted" style="font-size:0.8rem;">Data pribadi (nama, alamat, RT/RW, dll)
                                otomatis diambil dari database.</small>
                        </div>

                        {{-- Dynamic Additional Fields --}}
                        <div id="additional-fields"></div>

                        <div class="form-section-title"><i class="fas fa-sticky-note"></i> Keterangan</div>
                        <div class="form-group">
                            <label>Keterangan Internal</label>
                            <textarea name="keterangan" rows="2" class="form-control"
                                placeholder="Catatan internal (tidak tampil di surat)...">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                    <div class="feature-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-arrow-right mr-1"></i> Lanjut (Buat Draft)
                        </button>
                        <a href="{{ route('surat.index') }}" class="btn btn-reset">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="info-card">
                <h6><i class="fas fa-info-circle mr-1"></i> Cara Penggunaan</h6>
                <p>1. Pilih jenis surat dan penduduk pemohon.</p>
                <p>2. Isi field tambahan yang muncul otomatis sesuai jenis surat.</p>
                <p class="mb-0">3. Klik <strong>Lanjut</strong> untuk membuat draft. Setelah itu Anda bisa mengunduh berkas
                    akhirnya.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Mengamankan pembungkus TomSelect */
        .ts-wrapper.tom-select {
            position: relative !important;
        }

        /* Memaksa dropdown melayang di atas konten lain */
        .ts-dropdown {
            position: absolute !important;
            z-index: 1060 !important;
            /* Di atas standard elemen bootstrap */
            background: #ffffff !important;
            width: 100% !important;
            left: 0 !important;
            top: 100% !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
        }

        /* Mematikan efek crash layout pada list item */
        .ts-dropdown .ts-dropdown-content {
            max-height: 200px;
            overflow-y: auto;
        }

        /* Mengamankan area data tambahan di bawahnya */
        #additional-fields {
            clear: both;
            display: block;
            position: relative;
            z-index: 1;
        }

        .dynamic-fields-box {
            margin-top: 1.25rem;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #28a745;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const jenisFields = @json($jenisList->keyBy('id')->map->fields);
        const oldData = @json(old('data', []));
        const container = document.getElementById('additional-fields');
        const select = document.getElementById('jenis_select');

        function renderFields(id) {
            container.innerHTML = '';
            const fields = jenisFields[id] || [];
            if (!fields.length) return;
            const wrap = document.createElement('div');
            wrap.className = 'dynamic-fields-box';
            wrap.innerHTML = '<h6><i class="fas fa-pen mr-1"></i>Data Tambahan Surat</h6>';
            const row = document.createElement('div');
            row.className = 'form-row';
            fields.forEach(f => {
                const val = oldData[f.name] || '';
                const req = f.required ? '<span class="text-danger">*</span>' : '';
                const isLong = f.type === 'textarea';
                const input = isLong
                    ? `<textarea name="data[${f.name}]" rows="2" class="form-control" ${f.required ? 'required' : ''}>${val}</textarea>`
                    : `<input type="${f.type === 'number' ? 'number' : (f.type === 'date' ? 'date' : 'text')}" name="data[${f.name}]" class="form-control" value="${val}" ${f.required ? 'required' : ''}>`;
                const colClass = isLong ? 'col-12' : 'col-md-6';
                row.insertAdjacentHTML('beforeend',
                    `<div class="form-group ${colClass}"><label style="font-size:0.84rem;font-weight:700;color:#4A5568;">${f.label} ${req}</label>${input}</div>`);
            });
            wrap.appendChild(row);
            container.appendChild(wrap);
        }

        select.addEventListener('change', e => renderFields(e.target.value));
        if (select.value) renderFields(select.value);
    </script>
@endpush