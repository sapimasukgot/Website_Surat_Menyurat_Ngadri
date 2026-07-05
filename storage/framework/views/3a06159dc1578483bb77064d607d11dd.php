
<?php $__env->startSection('title', 'Profil'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Informasi Profil</h3></div>
            <form method="POST" action="<?php echo e(route('profile.update')); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <div class="card-body">
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
                <div class="card-footer"><button class="btn btn-primary">Simpan</button></div>
            </form>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/profile/edit.blade.php ENDPATH**/ ?>