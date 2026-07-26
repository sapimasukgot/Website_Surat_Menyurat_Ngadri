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

                        <div class="form-section-title"><i class="fas fa-user-tie"></i> Penandatangan</div>
                        <div class="form-group">
                            <label>Ditandatangani oleh <span class="text-danger">*</span></label>
                            <select name="penandatangan_role" class="form-control">
                                <option value="kepala_desa" @selected(old('penandatangan_role', 'kepala_desa') === 'kepala_desa')>
                                    Kepala Desa — {{ $penandatanganList['kepala_desa'] ?: 'belum diatur' }}
                                </option>
                                <option value="sekretaris_desa" @selected(old('penandatangan_role') === 'sekretaris_desa')>
                                    Sekretaris Desa — {{ $penandatanganList['sekretaris_desa'] ?: 'belum diatur' }}
                                </option>
                            </select>
                            <small class="text-muted" style="font-size:0.8rem;">Default Kepala Desa. Nama diambil dari
                                pengaturan perangkat desa pada dashboard.</small>
                        </div>

                        <div class="form-section-title"><i class="fas fa-heading"></i> Kop Surat</div>
                        <div class="form-group">
                            <input type="hidden" name="pakai_kop" value="0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="pakai_kop" name="pakai_kop"
                                    value="1" @checked(old('pakai_kop', '1') == '1')>
                                <label class="custom-control-label" for="pakai_kop"
                                    style="font-size:0.9rem;font-weight:600;color:#4A5568;">Gunakan kop surat</label>
                            </div>
                            <small class="text-muted" style="font-size:0.8rem;">Hilangkan centang untuk mencetak surat
                                <strong>tanpa kop</strong> (mis. Surat Pernyataan). Kop akan dihapus otomatis dari berkas
                                Word — template tidak perlu diganti.</small>
                        </div>

                        {{-- Dynamic Additional Fields --}}
                        <div id="additional-fields"></div>
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
        const pendudukSelect = document.querySelector('select[name="penduduk_id"]');
        const keluargaUrlTpl = @json(route('penduduk.keluarga', ':id'));
        let anakCache = { pendudukId: null, list: [] };

        function anakOptionsHtml(selected) {
            if (!anakCache.pendudukId) {
                return '<option value="">— Pilih pemohon terlebih dahulu —</option>';
            }
            if (!anakCache.list.length) {
                return '<option value="">— Tidak ada data anak dalam KK pemohon —</option>';
            }
            return '<option value="">— Pilih Anak —</option>' + anakCache.list.map(a =>
                `<option value="${a.id}" ${String(selected) === String(a.id) ? 'selected' : ''}>${esc(a.nik)} — ${esc(a.nama)}</option>`
            ).join('');
        }

        function refreshAnakSelects() {
            container.querySelectorAll('select.anak-kk-select').forEach(sel => {
                const selected = sel.value || sel.dataset.old || '';
                sel.innerHTML = anakOptionsHtml(selected);
            });
        }

        async function loadAnak(pendudukId) {
            anakCache = { pendudukId: pendudukId || null, list: [] };
            if (pendudukId) {
                try {
                    const res = await fetch(keluargaUrlTpl.replace(':id', pendudukId), {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) anakCache.list = (await res.json()).anak || [];
                } catch (e) { /* biarkan kosong */ }
            }
            refreshAnakSelects();
        }

        // Nilai lama & label field bisa mengandung tanda kutip — amankan sebelum
        // disisipkan ke dalam string HTML.
        function esc(nilai) {
            return String(nilai ?? '').replace(/[&<>"']/g, c => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));
        }

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
                let input;
                if (f.type === 'anak_kk') {
                    input = `<select name="data[${f.name}]" class="form-control anak-kk-select" data-old="${esc(val)}" ${f.required ? 'required' : ''}></select>
                        <small class="text-muted" style="font-size:0.78rem;">Daftar NIK anak diambil otomatis dari KK pemohon. Data anak (nama, NIK, TTL) otomatis masuk ke surat.</small>`;
                } else if (f.type === 'select') {
                    const opsi = (f.options || []).map(o =>
                        `<option value="${esc(o)}" ${String(val) === String(o) ? 'selected' : ''}>${esc(o)}</option>`).join('');
                    input = `<select name="data[${f.name}]" class="form-control field-select" data-field="${f.name}" ${f.required ? 'required' : ''}>
                        <option value="">— Pilih —</option>${opsi}</select>`;
                } else if (isLong) {
                    input = `<textarea name="data[${f.name}]" rows="2" class="form-control" ${f.required ? 'required' : ''}>${esc(val)}</textarea>`;
                } else {
                    input = `<input type="${f.type === 'number' ? 'number' : (f.type === 'date' ? 'date' : 'text')}" name="data[${f.name}]" class="form-control" value="${esc(val)}" ${f.required ? 'required' : ''}>`;
                }
                const colClass = isLong ? 'col-12' : 'col-md-6';
                row.insertAdjacentHTML('beforeend',
                    `<div class="form-group ${colClass}" data-field-name="${f.name}"><label style="font-size:0.84rem;font-weight:700;color:#4A5568;">${esc(f.label)} ${req}</label>${input}</div>`);
            });
            wrap.appendChild(row);
            container.appendChild(wrap);
            refreshAnakSelects();
            syncBlokFields();
        }

        /**
         * Field bertipe select bisa dipakai sebagai saklar blok pada template
         * (${nama_field} ... ${/nama_field}). Bila pilihannya bukan "Ya", field
         * lain yang hanya relevan untuk blok itu ikut disembunyikan supaya
         * operator tidak bingung mengisi data yang tidak akan tercetak.
         */
        const blokFields = @json(config('surat.blok_fields', []));

        function syncBlokFields() {
            container.querySelectorAll('select.field-select').forEach(sel => {
                const anggota = blokFields[sel.dataset.field];
                if (!anggota) return;
                const tampil = sel.value === 'Ya';
                anggota.forEach(nama => {
                    const grup = container.querySelector(`[data-field-name="${nama}"]`);
                    if (grup) grup.style.display = tampil ? '' : 'none';
                });
            });
        }

        container.addEventListener('change', e => {
            if (e.target.classList.contains('field-select')) syncBlokFields();
        });

        select.addEventListener('change', e => renderFields(e.target.value));
        pendudukSelect.addEventListener('change', e => loadAnak(e.target.value));
        if (pendudukSelect.value) loadAnak(pendudukSelect.value);
        if (select.value) renderFields(select.value);
    </script>
@endpush