
<?php $__env->startSection('title', 'Masuk'); ?>
<?php $__env->startSection('body_class', 'login-page-split'); ?>

<?php $__env->startSection('content'); ?>
    <div class="login-split-container">
        <!-- Left Column: Branding Showcase -->
        <div class="login-split-brand">
            <div class="brand-showcase">
                <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo <?php echo e(config('desa.nama')); ?>" class="brand-showcase-logo">
                <h1>Halo <?php echo e(config('desa.nama')); ?>! 👋</h1>
                <p>Sistem Administrasi Surat Menyurat Terpadu. Mempermudah pelayanan surat penduduk secara efisien, cepat,
                    dan transparan.</p>
            </div>
            <div class="brand-showcase-footer">
                &copy; 2026 <?php echo e(config('desa.nama')); ?>. All rights reserved.
            </div>
        </div>

        <!-- Right Column: Login Form -->
        <div class="login-split-form">
            <div class="login-form-inner">
                <div class="login-form-logo-section">
                    <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo" class="login-form-logo">
                    <span class="login-form-title">Surat Desa</span>
                </div>

                <h2 class="login-welcome-title">Selamat Datang!</h2>
                <p class="login-welcome-subtitle">Masukkan kredensial Anda untuk masuk ke sistem.</p>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger py-2 mb-4" style="border-radius: 8px;">
                        <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($errors->first()); ?>

                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('login')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="split-input-group">
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="split-input"
                            placeholder="Masukkan Email" required autofocus>
                    </div>

                    <div class="split-input-group">
                        <input type="password" name="password" class="split-input" placeholder="Masukkan Kata Sandi"
                            required>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                            <label class="custom-control-label text-muted text-sm"
                                style="font-weight: 500; cursor: pointer;" for="remember">Ingat saya</label>
                        </div>
                    </div>

                    <button type="submit" class="btn split-login-btn">Masuk Sistem</button>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/auth/login.blade.php ENDPATH**/ ?>