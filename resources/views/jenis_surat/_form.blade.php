@csrf
<div class="form-section-title"><i class="fas fa-file-alt"></i> Informasi Dasar Surat</div>
<div class="form-row">
    <div class="form-group col-md-6">
        <label>Nama Surat <span class="text-danger">*</span></label>
        <input type="text" name="nama_surat" class="form-control @error('nama_surat') is-invalid @enderror"
            value="{{ old('nama_surat', $jenisSurat->nama_surat) }}" required>
        @error('nama_surat') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="form-group col-md-3">
        <label>Kode Surat <span class="text-danger">*</span></label>
        <input type="text" name="kode_surat"
            class="form-control text-uppercase @error('kode_surat') is-invalid @enderror"
            value="{{ old('kode_surat', $jenisSurat->kode_surat) }}" required>
        @error('kode_surat') <span class="invalid-feedback">{{ $message }}</span> @enderror
        <small class="form-text text-muted">Kode singkat, mis. <code>SKTM</code>.</small>
    </div>
    <div class="form-group col-md-3">
        <label>Kode Klasifikasi</label>
        <input type="text" name="kode_klasifikasi"
            class="form-control @error('kode_klasifikasi') is-invalid @enderror"
            value="{{ old('kode_klasifikasi', $jenisSurat->kode_klasifikasi) }}" placeholder="mis. 470">
        @error('kode_klasifikasi') <span class="invalid-feedback">{{ $message }}</span> @enderror
        <small class="form-text text-muted">Angka klasifikasi arsip yang muncul di
            <strong>awal nomor surat</strong> (mis. <code>470</code>, <code>422.5</code>). Kosongkan bila jenis surat ini
            tidak memakainya.</small>
    </div>
</div>

<div class="alert alert-light border" style="font-size:0.83rem;">
    <i class="fas fa-hashtag mr-1 text-muted"></i> Nomor surat akan otomatis dibuat dengan format
    <strong>{{ config('nomor_surat.default_format') === '{kode_klasifikasi}/{urut3}/{kode_desa}/{tahun}' ? 'kode klasifikasi / nomor urut / kode desa / tahun' : config('nomor_surat.default_format') }}</strong>
    — contoh: <code>{{ old('kode_klasifikasi', $jenisSurat->kode_klasifikasi) ?: '470' }}/007/{{ config('desa.kode') }}/{{ date('Y') }}</code>.
</div>

<div class="form-group">
    <label>Deskripsi</label>
    <textarea name="deskripsi" rows="2" class="form-control"
        placeholder="Keterangan singkat tentang jenis surat ini...">{{ old('deskripsi', $jenisSurat->deskripsi) }}</textarea>
</div>

<div class="form-section-title mt-2"><i class="fas fa-file-word"></i> Template Dokumen</div>
<div class="form-group">
    <label>Template Surat (.docx)</label>
    <div class="custom-file">
        <input type="file" name="template" id="template"
            class="custom-file-input @error('template') is-invalid @enderror" accept=".docx">
        <label class="custom-file-label" for="template">
            {{ $jenisSurat->template_original_name ?? 'Pilih berkas .docx...' }}
        </label>
        @error('template') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    @if ($jenisSurat->hasTemplate())
        <small class="text-muted">Template saat ini: <a
                href="{{ route('jenis-surat.template', $jenisSurat) }}">{{ $jenisSurat->template_original_name }}</a>.
            Unggah berkas baru untuk mengganti.</small>
    @endif
    <small class="form-text text-muted">Gunakan placeholder seperti <code>${nomor_surat}</code>, <code>${nama}</code>,
        <code>${nik}</code>, <code>${alamat}</code>, dll di dalam dokumen Word.</small>
</div>

<div class="form-section-title mt-2"><i class="fas fa-list-ul"></i> Field Tambahan (Dinamis)</div>
<p class="text-muted" style="font-size:0.84rem;">Field ini akan tampil saat pembuatan surat. Placeholder otomatis dari
    label (mis. "Nama Usaha" &rarr; <code>${nama_usaha}</code>).</p>
<p class="text-muted" style="font-size:0.84rem;">Tipe <strong>Anak (satu KK)</strong> menampilkan dropdown NIK anak yang
    satu KK dengan pemohon. Jika label field-nya "Anak", placeholder yang tersedia: <code>${anak}</code>,
    <code>${nama_anak}</code>, <code>${nik_anak}</code>, <code>${tempat_lahir_anak}</code>,
    <code>${tanggal_lahir_anak}</code>, <code>${jenis_kelamin_anak}</code>.</p>
<p class="text-muted" style="font-size:0.84rem;">Tipe <strong>Pilihan (dropdown)</strong> menampilkan daftar pilihan yang
    Anda tentukan sendiri di kolom Pilihan (pisahkan dengan koma, mis. <code>Ya, Tidak</code>). Selain mengisi
    placeholder biasa, tipe ini bisa dipakai untuk <strong>menampilkan/menyembunyikan satu blok</strong> di template:
    bungkus bagian yang opsional di file Word dengan <code>${nama_field}</code> ... <code>${/nama_field}</code>, maka blok
    itu hanya ikut tercetak bila pilihannya <em>Ya</em>. Kedua penanda harus berada di
    <strong>paragraf/barisnya sendiri</strong> di dalam dokumen Word.</p>

<div class="table-responsive">
<table class="table table-sm" id="fields-table">
    <thead>
        <tr>
            <th>Label</th>
            <th style="width:40%">Tipe &amp; Pilihan (khusus dropdown)</th>
            <th style="width:1%; white-space:nowrap;" class="text-center">Wajib</th>
            <th style="width:1%; white-space:nowrap;"></th>
        </tr>
    </thead>
    <tbody>
        @php($rows = is_array($r = old('fields', $jenisSurat->fields ?? [])) ? $r : [])
        @forelse ($rows as $i => $f)
            @php($opts = is_array($f['options'] ?? null) ? implode(', ', $f['options']) : ($f['options'] ?? ''))
            <tr>
                <td>
                    {{-- Nama field (placeholder ${...}) dikunci agar tidak berubah saat labelnya disunting. --}}
                    <input type="hidden" name="fields[{{ $i }}][name]" value="{{ $f['name'] ?? '' }}">
                    <input type="text" name="fields[{{ $i }}][label]" class="form-control form-control-sm"
                        value="{{ $f['label'] ?? '' }}">
                    @if (!empty($f['name']))
                        <small class="text-muted" style="font-size:0.75rem;">Placeholder:
                            <code>${{ '{' . $f['name'] . '}' }}</code></small>
                    @endif
                </td>
                <td>
                    <select name="fields[{{ $i }}][type]" class="form-control form-control-sm field-type">
                        @foreach (\App\Models\JenisSurat::FIELD_TYPE_LABELS as $val => $lbl)
                            <option value="{{ $val }}" @selected(($f['type'] ?? 'text') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="fields[{{ $i }}][options]" class="form-control form-control-sm field-options mt-1"
                        value="{{ $opts }}" placeholder="Pilihan, mis. Ya, Tidak"
                        @style(['display:none' => ($f['type'] ?? 'text') !== 'select'])>
                </td>
                <td class="text-center" style="white-space:nowrap;"><input type="checkbox"
                        name="fields[{{ $i }}][required]" value="1" @checked($f['required'] ?? false)>
                </td>
                <td style="white-space:nowrap;"><button type="button" class="btn btn-xs btn-danger"
                        onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td>
            </tr>
        @empty
        @endforelse
    </tbody>
</table>
</div>
<button type="button" class="header-btn btn-import mt-2" id="add-field" style="font-size:0.82rem;"><i
        class="fas fa-plus"></i> Tambah Field</button>

<div class="form-section-title mt-3"><i class="fas fa-toggle-on"></i> Status</div>
<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
            @checked(old('is_active', $jenisSurat->is_active ?? true))>
        <label class="custom-control-label" for="is_active"
            style="font-size:0.9rem;font-weight:600;color:#4A5568;">Jenis surat ini aktif dan bisa digunakan</label>
    </div>
</div>

@push('scripts')
    <script>
        let idx = {{ count($rows) }};
        const typeOptions = @json(\App\Models\JenisSurat::FIELD_TYPE_LABELS);

        // Kolom "Pilihan" hanya relevan untuk tipe select — sembunyikan untuk tipe lain.
        function syncOptionsColumn(row) {
            const type = row.querySelector('.field-type');
            const options = row.querySelector('.field-options');
            if (!type || !options) return;
            options.style.display = type.value === 'select' ? '' : 'none';
        }

        document.querySelector('#fields-table tbody').addEventListener('change', function (e) {
            if (e.target.classList.contains('field-type')) syncOptionsColumn(e.target.closest('tr'));
        });

        document.getElementById('add-field').addEventListener('click', function () {
            const tr = document.createElement('tr');
            const opsi = Object.entries(typeOptions)
                .map(([val, lbl]) => `<option value="${val}">${lbl}</option>`).join('');
            tr.innerHTML = `
                <td><input type="hidden" name="fields[${idx}][name]" value="">
                    <input type="text" name="fields[${idx}][label]" class="form-control form-control-sm" placeholder="Contoh: Keperluan"></td>
                <td><select name="fields[${idx}][type]" class="form-control form-control-sm field-type">${opsi}</select>
                    <input type="text" name="fields[${idx}][options]" class="form-control form-control-sm field-options mt-1" placeholder="Pilihan, mis. Ya, Tidak" style="display:none"></td>
                <td class="text-center" style="white-space:nowrap;"><input type="checkbox" name="fields[${idx}][required]" value="1"></td>
                <td style="white-space:nowrap;"><button type="button" class="btn btn-xs btn-danger" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td>`;
            document.querySelector('#fields-table tbody').appendChild(tr);
            idx++;
        });

        document.querySelectorAll('#fields-table tbody tr').forEach(syncOptionsColumn);

        const tpl = document.getElementById('template');
        if (tpl) tpl.addEventListener('change', e => e.target.nextElementSibling.textContent = e.target.files[0]?.name || 'Pilih berkas .docx...');
    </script>
@endpush