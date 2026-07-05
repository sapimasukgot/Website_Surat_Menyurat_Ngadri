@csrf
<div class="form-row">
    <div class="form-group col-md-8">
        <label>Nama Surat <span class="text-danger">*</span></label>
        <input type="text" name="nama_surat" class="form-control @error('nama_surat') is-invalid @enderror"
               value="{{ old('nama_surat', $jenisSurat->nama_surat) }}" required>
        @error('nama_surat') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="form-group col-md-4">
        <label>Kode Surat <span class="text-danger">*</span></label>
        <input type="text" name="kode_surat" class="form-control text-uppercase @error('kode_surat') is-invalid @enderror"
               value="{{ old('kode_surat', $jenisSurat->kode_surat) }}" required>
        @error('kode_surat') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
</div>

<div class="form-group">
    <label>Deskripsi</label>
    <textarea name="deskripsi" rows="2" class="form-control">{{ old('deskripsi', $jenisSurat->deskripsi) }}</textarea>
</div>

<div class="form-group">
    <label>Template Surat (.docx)</label>
    <div class="custom-file">
        <input type="file" name="template" id="template" class="custom-file-input @error('template') is-invalid @enderror" accept=".docx">
        <label class="custom-file-label" for="template">
            {{ $jenisSurat->template_original_name ?? 'Pilih berkas .docx...' }}
        </label>
        @error('template') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    @if ($jenisSurat->hasTemplate())
        <small class="text-muted">Template saat ini: <a href="{{ route('jenis-surat.template', $jenisSurat) }}">{{ $jenisSurat->template_original_name }}</a>. Unggah berkas baru untuk mengganti.</small>
    @endif
    <small class="form-text text-muted">Gunakan placeholder seperti <code>${nomor_surat}</code>, <code>${nama}</code>, <code>${nik}</code>, <code>${alamat}</code>, dll di dalam dokumen Word.</small>
</div>

<hr>
<h6><i class="fas fa-list-ul mr-1"></i> Field Tambahan (Dinamis)</h6>
<p class="text-muted small">Field ini akan tampil saat pembuatan surat. Nama placeholder = versi slug dari label (mis. "Nama Usaha" &rarr; <code>${nama_usaha}</code>).</p>

<table class="table table-sm" id="fields-table">
    <thead><tr><th style="width:40%">Label</th><th style="width:25%">Tipe</th><th style="width:20%">Wajib</th><th></th></tr></thead>
    <tbody>
        @php($rows = old('fields', $jenisSurat->fields ?? []))
        @forelse ($rows as $i => $f)
            <tr>
                <td><input type="text" name="fields[{{ $i }}][label]" class="form-control form-control-sm" value="{{ $f['label'] ?? '' }}"></td>
                <td>
                    <select name="fields[{{ $i }}][type]" class="form-control form-control-sm">
                        @foreach (['text' => 'Teks', 'textarea' => 'Teks Panjang', 'date' => 'Tanggal', 'number' => 'Angka'] as $val => $lbl)
                            <option value="{{ $val }}" @selected(($f['type'] ?? 'text') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="checkbox" name="fields[{{ $i }}][required]" value="1" @checked($f['required'] ?? false)></td>
                <td><button type="button" class="btn btn-xs btn-danger" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td>
            </tr>
        @empty
        @endforelse
    </tbody>
</table>
<button type="button" class="btn btn-sm btn-outline-primary" id="add-field"><i class="fas fa-plus"></i> Tambah Field</button>

<div class="form-group mt-3">
    <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $jenisSurat->is_active ?? true))>
        <label class="custom-control-label" for="is_active">Aktif</label>
    </div>
</div>

@push('scripts')
<script>
    let idx = {{ count($rows) }};
    document.getElementById('add-field').addEventListener('click', function () {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="fields[${idx}][label]" class="form-control form-control-sm"></td>
            <td><select name="fields[${idx}][type]" class="form-control form-control-sm">
                <option value="text">Teks</option><option value="textarea">Teks Panjang</option>
                <option value="date">Tanggal</option><option value="number">Angka</option></select></td>
            <td><input type="checkbox" name="fields[${idx}][required]" value="1"></td>
            <td><button type="button" class="btn btn-xs btn-danger" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td>`;
        document.querySelector('#fields-table tbody').appendChild(tr);
        idx++;
    });
    const tpl = document.getElementById('template');
    if (tpl) tpl.addEventListener('change', e => e.target.nextElementSibling.textContent = e.target.files[0]?.name || 'Pilih berkas .docx...');
</script>
@endpush
