<?php ($isPenduduk = request()->routeIs('penduduk.*')); ?>
<?php ($isJenis = request()->routeIs('jenis-surat.*')); ?>
<?php ($isSurat = request()->routeIs('surat.*')); ?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo e(route('dashboard')); ?>" class="brand-link">
        <img src="<?php echo e(asset('images/LOGO DESA NGADRI NO BG.png')); ?>" alt="Logo <?php echo e(config('desa.nama')); ?>"
            class="brand-image" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));">
        <span class="brand-text font-weight-bold">Surat Desa</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <?php if(Auth::user()->photo_url): ?>
                    <img src="<?php echo e(Auth::user()->photo_url); ?>?v=<?php echo e(Auth::user()->updated_at?->timestamp); ?>"
                        class="img-circle elevation-1" alt="Foto"
                        style="width:34px;height:34px;object-fit:cover;">
                <?php else: ?>
                    <i class="fas fa-user-circle fa-2x text-white-50"></i>
                <?php endif; ?>
            </div>
            <div class="info">
                <span class="d-block text-white"><?php echo e(Auth::user()->name); ?></span>
                <small class="text-white-50"><?php echo e(Auth::user()->jabatan ?? 'Administrator'); ?></small>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="<?php echo e(route('dashboard')); ?>"
                        class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-header text-uppercase">Master Data</li>
                <li class="nav-item">
                    <a href="<?php echo e(route('penduduk.index')); ?>" class="nav-link <?php echo e($isPenduduk ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Data Penduduk</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('jenis-surat.index')); ?>" class="nav-link <?php echo e($isJenis ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>Jenis Surat</p>
                    </a>
                </li>
                <li class="nav-header text-uppercase">Pelayanan</li>
                <li class="nav-item">
                    <a href="<?php echo e(route('surat.create')); ?>"
                        class="nav-link <?php echo e(request()->routeIs('surat.create') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-file-signature"></i>
                        <p>Buat Surat</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('surat.index')); ?>"
                        class="nav-link <?php echo e(request()->routeIs('surat.index') || request()->routeIs('surat.show') || request()->routeIs('surat.edit') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Riwayat Surat</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside><?php /**PATH C:\Users\AhsanDz\Claude\Projects\Surat_menyurat_Ngadri\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>