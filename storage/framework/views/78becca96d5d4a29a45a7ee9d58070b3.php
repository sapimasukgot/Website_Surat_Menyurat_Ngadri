<?php $__env->startSection('title', 'Profil'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Informasi Profil</h3></div>
            <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <div class="card-body">
                    <div class="form-group text-center">
                        <img id="photo-preview"
                            src="<?php echo e($user->photo_url ? $user->photo_url.'?v='.$user->updated_at?->timestamp : ''); ?>"
                            alt="Foto Profil"
                            style="width:96px;height:96px;object-fit:cover;border-radius:50%;border:3px solid #e2e8f0;<?php echo e($user->photo_url ? '' : 'display:none;'); ?>">
                        <div id="photo-initial" class="d-inline-flex align-items-center justify-content-center"
                            style="width:96px;height:96px;border-radius:50%;background:#e2e8f0;font-size:2rem;font-weight:700;color:#4A5568;<?php echo e($user->photo_url ? 'display:none;' : ''); ?>">
                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                        </div>
                        <div id="photo-preview-note" class="text-muted small mt-1" style="display:none;">
                            Pratinjau — foto tersimpan setelah klik <strong>Simpan</strong>.
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Foto Profil</label>
                        <input type="file" name="photo" id="photo-input" accept=".png,.jpg,.jpeg"
                            class="form-control-file <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback d-block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="text-muted">PNG/JPG, maks. 2 MB.</small>
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $user->name)); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="<?php echo e(old('jabatan', $user->jabatan)); ?>">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $user->email)); ?>" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
            <?php if($user->photo_url): ?>
                <form method="POST" action="<?php echo e(route('profile.photo.destroy')); ?>" class="px-3 pb-3 mb-0"
                    onsubmit="return confirm('Hapus foto profil?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt mr-1"></i> Hapus Foto
                        Profil</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Ubah Kata Sandi</h3></div>
            <form method="POST" action="<?php echo e(route('password.update')); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\AhsanDz\Documents\GitHub\Website_Surat_Menyurat_Ngadri\resources\views/profile/edit.blade.php ENDPATH**/ ?>