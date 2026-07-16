@extends('layouts.app')
@section('title', 'Profil')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Informasi Profil</h3></div>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf @method('PATCH')
                <div class="card-body">
                    <div class="form-group text-center">
                        <img id="photo-preview"
                            src="{{ $user->photo_url ? $user->photo_url.'?v='.$user->updated_at?->timestamp : '' }}"
                            alt="Foto Profil"
                            style="width:96px;height:96px;object-fit:cover;border-radius:50%;border:3px solid #e2e8f0;{{ $user->photo_url ? '' : 'display:none;' }}">
                        <div id="photo-initial" class="d-inline-flex align-items-center justify-content-center"
                            style="width:96px;height:96px;border-radius:50%;background:#e2e8f0;font-size:2rem;font-weight:700;color:#4A5568;{{ $user->photo_url ? 'display:none;' : '' }}">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div id="photo-preview-note" class="text-muted small mt-1" style="display:none;">
                            Pratinjau — foto tersimpan setelah klik <strong>Simpan</strong>.
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Foto Profil</label>
                        <input type="file" name="photo" id="photo-input" accept=".png,.jpg,.jpeg"
                            class="form-control-file @error('photo') is-invalid @enderror">
                        @error('photo') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        <small class="text-muted">PNG/JPG, maks. 2 MB.</small>
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $user->jabatan) }}">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
            @if ($user->photo_url)
                <form method="POST" action="{{ route('profile.photo.destroy') }}" class="px-3 pb-3 mb-0"
                    onsubmit="return confirm('Hapus foto profil?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt mr-1"></i> Hapus Foto
                        Profil</button>
                </form>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Ubah Kata Sandi</h3></div>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label>Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kata Sandi Baru</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Kata Sandi</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="card-footer"><button class="btn btn-primary">Perbarui</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Pratinjau foto profil sebelum diunggah
        document.getElementById('photo-input').addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.getElementById('photo-preview');
                img.src = e.target.result;
                img.style.display = 'inline-block';
                document.getElementById('photo-initial').style.display = 'none';
                document.getElementById('photo-preview-note').style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    </script>
@endpush
