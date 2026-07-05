<nav class="main-header navbar navbar-expand navbar-primary navbar-dark">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?php echo e(route('dashboard')); ?>" class="nav-link">Beranda</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-user"></i> <?php echo e(Auth::user()->name); ?> <i class="fas fa-caret-down"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <span class="dropdown-item-text text-muted small"><?php echo e(Auth::user()->jabatan ?? 'Admin'); ?></span>
                <div class="dropdown-divider"></div>
                <a href="<?php echo e(route('profile.edit')); ?>" class="dropdown-item"><i class="fas fa-id-card mr-2"></i> Profil</a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt mr-2"></i> Keluar</button>
                </form>
            </div>
        </li>
    </ul>
</nav>
<?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/partials/navbar.blade.php ENDPATH**/ ?>