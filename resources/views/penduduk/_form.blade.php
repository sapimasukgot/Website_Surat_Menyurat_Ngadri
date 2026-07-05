@csrf
<div class="form-row">
    <div class="form-group col-md-6">
        <label>NIK <span class="text-danger">*</span></label>
        <input type="text" name="nik" maxlength="16" class="form-control @error('nik') is-invalid @enderror"
               value="{{ old('nik', $penduduk->nik) }}" required>
        @error('nik') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="form-group col-md-6">
        <label>Nomor KK <span class="text-danger">*</span></label>
        <input type="text" name="no_kk" maxlength="16" class="form-control @error('no_kk') is-invalid @enderror"
               value="{{ old('no_kk', $penduduk->no_kk) }}" required>
        @error('no_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
</div>

<div class="form-group">
    <label>Nama Lengkap <span class="text-danger">*</span></label>
    <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror"
           value="{{ old('nama_lengkap', $penduduk->nama_lengkap) }}" required>
    @error('nama_lengkap') <span class="invalid-feedback">{{ $message }}</span> @enderror
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>Tempat Lahir</label>
        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $penduduk->tempat_lahir) }}">
    </div>
    <div class="form-group col-md-3">
        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" class="form-control"
               value="{{ old('tanggal_lahir', $penduduk->tanggal_lahir?->format('Y-m-d')) }}">
    </div>
    <div class="form-group col-md-3">
        <label>Jenis Kelamin <span class="text-danger">*</span></label>
        <select name="jenis_kelamin" class="form-control" required>
            @foreach (\App\Models\Penduduk::JENIS_KELAMIN as $k => $v)
                <option value="{{ $k }}" @selected(old('jenis_kelamin', $penduduk->jenis_kelamin) === $k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-3">
        <label>Agama</label>
        <select name="agama" class="form-control">
            <option value="">-</option>
            @foreach (\App\Models\Penduduk::AGAMA as $a)
                <option value="{{ $a }}" @selected(old('agama', $penduduk->agama) === $a)>{{ $a }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-3">
        <label>Pendidikan</label>
        <input type="text" name="pendidikan" class="form-control" value="{{ old('pendidikan', $penduduk->pendidikan) }}">
    </div>
    <div class="form-group col-md-3">
        <label>Pekerjaan</label>
        <input type="text" name="pekerjaan" class="form-control" value="{{ old('pekerjaan', $penduduk->pekerjaan) }}">
    </div>
    <div class="form-group col-md-3">
        <label>Status Kawin <span class="text-danger">*</span></label>
        <select name="status_kawin" class="form-control" required>
            @foreach (\App\Models\Penduduk::STATUS_KAWIN as $s)
                <option value="{{ $s }}" @selected(old('status_kawin', $penduduk->status_kawin) === $s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label>Alamat <span class="text-danger">*</span></label>
    <textarea name="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $penduduk->alamat) }}</textarea>
    @error('alamat') <span class="invalid-feedback">{{ $message }}</span> @enderror
</div>

<div class="form-row">
    <div class="form-group col-md-2">
        <label>RT</label>
        <input type="text" name="rt" maxlength="3" class="form-control" value="{{ old('rt', $penduduk->rt) }}">
    </div>
    <div class="form-group col-md-2">
        <label>RW</label>
        <input type="text" name="rw" maxlength="3" class="form-control" value="{{ old('rw', $penduduk->rw) }}">
    </div>
    <div class="form-group col-md-4">
        <label>Dusun</label>
        <input type="text" name="dusun" class="form-control" value="{{ old('dusun', $penduduk->dusun) }}">
    </div>
    <div class="form-group col-md-4">
        <label>Nomor HP</label>
        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $penduduk->no_hp) }}">
    </div>
</div>
