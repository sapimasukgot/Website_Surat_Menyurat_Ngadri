@extends('layouts.app')
@section('title', 'Buat Surat')

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header"><h3 class="card-title"><i class="fas fa-file-signature mr-1"></i> Buat Surat Baru</h3></div>
    <form action="{{ route('surat.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Jenis Surat <span class="text-danger">*</span></label>
                    <select name="jenis_surat_id" id="jenis_select" class="form-control @error('jenis_surat_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Jenis Surat --</option>
                        @foreach ($jenisList as $j)
                            <option value="{{ $j->id }}" @selected(old('jenis_surat_id') == $j->id)>{{ $j->nama_surat }}</option>
                        @endforeach
                    </select>
                    @error('jenis_surat_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-6">
                    <label>Tanggal Surat <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_surat" class="form-control" value="{{ old('tanggal_surat', date('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label>Penduduk <span class="text-danger">*</span></label>
                <select name="penduduk_id" class="form-control tom-select @error('penduduk_id') is-invalid @enderror"
                        data-placeholder="Cari NIK / Nama..." required>
                    <option value="">-- Pilih Penduduk --</option>
                    @foreach ($penduduks as $p)
                        <option value="{{ $p->id }}" @selected(old('penduduk_id') == $p->id)>{{ $p->nik }} - {{ $p->nama_lengkap }}</option>
                    @endforeach
                </select>
                @error('penduduk_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                <small class="text-muted">Data pribadi (nama, alamat, RT/RW, dll) otomatis diambil dari database.</small>
            </div>

            <div id="additional-fields"></div>

            <div class="form-group">
                <label>Keterangan (internal)</label>
                <textarea name="keterangan" rows="2" class="form-control">{{ old('keterangan') }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary"><i class="fas fa-arrow-right"></i> Lanjut (Buat Draft)</button>
            <a href="{{ route('surat.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

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
        wrap.className = 'border rounded p-3 bg-light mb-3';
        wrap.innerHTML = '<h6 class="text-primary"><i class="fas fa-pen mr-1"></i> Data Tambahan Surat</h6>';
        fields.forEach(f => {
            const val = oldData[f.name] || '';
            const req = f.required ? '<span class="text-danger">*</span>' : '';
            const input = f.type === 'textarea'
                ? `<textarea name="data[${f.name}]" rows="2" class="form-control" ${f.required ? 'required' : ''}>${val}</textarea>`
                : `<input type="${f.type === 'number' ? 'number' : (f.type === 'date' ? 'date' : 'text')}" name="data[${f.name}]" class="form-control" value="${val}" ${f.required ? 'required' : ''}>`;
            wrap.insertAdjacentHTML('beforeend',
                `<div class="form-group mb-2"><label class="mb-1">${f.label} ${req}</label>${input}</div>`);
        });
        container.appendChild(wrap);
    }

    select.addEventListener('change', e => renderFields(e.target.value));
    if (select.value) renderFields(select.value);
</script>
@endpush
