
<?php $__env->startSection('title', 'Profil'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Informasi Profil</h3>
                </div>
                <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <div class="card-body">
                        <!-- Foto Profil Section -->
                        <div class="form-group text-center mb-4">
                            <label class="d-block mb-3">Foto Profil</label>
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <div class="position-relative mb-3">
                                    <?php if($user->avatar): ?>
                                        <img id="avatar-preview" src="<?php echo e(asset('storage/' . $user->avatar)); ?>"
                                            class="img-circle elevation-2 border"
                                            style="width: 120px; height: 120px; object-fit: cover; border-width: 3px !important; border-color: var(--brand-primary) !important;">
                                    <?php else: ?>
                                        <div id="avatar-placeholder"
                                            class="img-circle elevation-2 d-flex align-items-center justify-content-center bg-light border"
                                            style="width: 120px; height: 120px; border-width: 3px !important; border-color: rgba(0,0,0,0.1) !important;">
                                            <i class="fas fa-user text-muted" style="font-size: 3.5rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex align-items-center" style="gap: 10px;">
                                    <label for="avatar-input" class="btn btn-outline-primary btn-sm mb-0"
                                        style="cursor: pointer;">
                                        <i class="fas fa-upload mr-1"></i> Pilih Foto
                                    </label>
                                    <input type="file" id="avatar-input" name="avatar" class="d-none" accept="image/*"
                                        onchange="previewAvatar(this)">

                                    <?php if($user->avatar): ?>
                                        <button id="btn-remove-avatar" type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="removeAvatar()">
                                            <i class="fas fa-trash mr-1"></i> Hapus Foto
                                        </button>
                                    <?php endif; ?>

                                    <input type="hidden" name="delete_avatar" id="delete-avatar-input" value="0">
                                </div>
                                <small class="text-muted mt-2">Format: JPG, JPEG, PNG, GIF, SVG. Max: 2MB.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $user->name)); ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Jabatan</label>
                            <input type="text" name="jabatan" class="form-control"
                                value="<?php echo e(old('jabatan', $user->jabatan)); ?>">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $user->email)); ?>"
                                required>
                        </div>
                    </div>
                    <div class="card-footer"><button class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Ubah Kata Sandi</h3>
                </div>
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

    <?php $__env->startPush('scripts'); ?>
        <script>
            function previewAvatar(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        var preview = document.getElementById('avatar-preview');
                        var placeholder = document.getElementById('avatar-placeholder');

                        if (placeholder) {
                            var img = document.createElement('img');
                            img.id = 'avatar-preview';
                            img.src = e.target.result;
                            img.className = 'img-circle elevation-2 border';
                            img.style.width = '120px';
                            img.style.height = '120px';
                            img.style.objectFit = 'cover';
                            img.style.borderWidth = '3px';
                            img.style.borderColor = 'var(--brand-primary)';

                            placeholder.parentNode.replaceChild(img, placeholder);
                        } else if (preview) {
                            preview.src = e.target.result;
                        }

                        // Add Delete button if not present
                        var btnRemove = document.getElementById('btn-remove-avatar');
                        if (!btnRemove) {
                            var deleteBtn = document.createElement('button');
                            deleteBtn.id = 'btn-remove-avatar';
                            deleteBtn.type = 'button';
                            deleteBtn.className = 'btn btn-outline-danger btn-sm';
                            deleteBtn.innerHTML = '<i class="fas fa-trash mr-1"></i> Hapus Foto';
                            deleteBtn.onclick = removeAvatar;

                            document.querySelector('#avatar-input').parentNode.appendChild(deleteBtn);
                        }
                    }

                    reader.readAsDataURL(input.files[0]);
                    document.getElementById('delete-avatar-input').value = '0';
                }
            }

            function removeAvatar() {
                var preview = document.getElementById('avatar-preview');
                var placeholder = document.getElementById('avatar-placeholder');

                var placeholderDiv = document.createElement('div');
                placeholderDiv.id = 'avatar-placeholder';
                placeholderDiv.className = 'img-circle elevation-2 d-flex align-items-center justify-content-center bg-light border';
                placeholderDiv.style.width = '120px';
                placeholderDiv.style.height = '120px';
                placeholderDiv.style.borderWidth = '3px';
                placeholderDiv.style.borderColor = 'rgba(0,0,0,0.1)';
                placeholderDiv.innerHTML = '<i class="fas fa-user text-muted" style="font-size: 3.5rem;"></i>';

                if (preview) {
                    preview.parentNode.replaceChild(placeholderDiv, preview);
                }

                var btnRemove = document.getElementById('btn-remove-avatar');
                if (btnRemove) {
                    btnRemove.remove();
                }

                document.getElementById('avatar-input').value = '';
                document.getElementById('delete-avatar-input').value = '1';
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/profile/edit.blade.php ENDPATH**/ ?>