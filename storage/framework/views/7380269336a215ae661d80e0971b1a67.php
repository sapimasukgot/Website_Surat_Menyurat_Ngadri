
<?php $__env->startSection('title', 'Data Penduduk'); ?>

<?php
    function sortLink($column, $label, $sort, $direction)
    {
        $dir = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
        $icon = $sort === $column ? ($direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort';
        $q = array_merge(request()->query(), ['sort' => $column, 'direction' => $dir]);
        return '<a href="' . route('penduduk.index', $q) . '" class="text-dark text-decoration-none font-weight-bold">' . $label . ' <i class="fas ' . $icon . ' text-muted small"></i></a>';
    }
?>

<?php $__env->startSection('content'); ?>
    <div class="feature-card">
        
        <div class="feature-card-header">
            <h3 class="feature-card-title">
                <i class="fas fa-users"></i> Daftar Penduduk
            </h3>
            <div class="d-flex align-items-center gap-2" style="gap: 0.5rem;">
                <a href="<?php echo e(route('penduduk.create')); ?>" class="header-btn btn-add">
                    <i class="fas fa-plus"></i> Tambah
                </a>
                <a href="<?php echo e(route('penduduk.import.form')); ?>" class="header-btn btn-import">
                    <i class="fas fa-file-import"></i> Import
                </a>
                <a href="<?php echo e(route('penduduk.export')); ?>" class="header-btn btn-export">
                    <i class="fas fa-file-excel"></i> Export
                </a>
            </div>
        </div>

        <div class="feature-card-body">
            
            <form method="GET">
                <div class="filter-bar">
                    <div class="filter-group">
                        <label>Pencarian</label>
                        <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control"
                            placeholder="NIK / No KK / Nama">
                    </div>
                    <div class="filter-group">
                        <label>Dusun</label>
                        <select name="dusun" class="form-control">
                            <option value="">Semua</option>
                            <?php $__currentLoopData = $dusunList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($d); ?>" <?php if(request('dusun') === $d): echo 'selected'; endif; ?>><?php echo e($d); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="filter-group" style="max-width:170px">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control">
                            <option value="">Semua</option>
                            <option value="L" <?php if(request('jenis_kelamin') === 'L'): echo 'selected'; endif; ?>>Laki-laki</option>
                            <option value="P" <?php if(request('jenis_kelamin') === 'P'): echo 'selected'; endif; ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-filter"><i class="fas fa-search"></i> Cari</button>
                        <a href="<?php echo e(route('penduduk.index')); ?>" class="btn btn-reset">Reset</a>
                    </div>
                </div>
            </form>

            
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th><?php echo sortLink('nik', 'NIK', $sort, $direction); ?></th>
                            <th><?php echo sortLink('nama_lengkap', 'Nama Lengkap', $sort, $direction); ?></th>
                            <th>Kelamin</th>
                            <th>RT/RW</th>
                            <th><?php echo sortLink('dusun', 'Dusun', $sort, $direction); ?></th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $penduduks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><span class="nik-code"><?php echo e($p->nik); ?></span></td>
                                <td>
                                    <span class="name-primary"><?php echo e($p->nama_lengkap); ?></span>
                                </td>
                                <td>
                                    <?php if($p->jenis_kelamin === 'L'): ?>
                                        <span class="badge-modern badge-blue"><i class="fas fa-mars"></i> L</span>
                                    <?php else: ?>
                                        <span class="badge-modern badge-red"><i class="fas fa-venus"></i> P</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-weight:600;color:#4A5568;"><?php echo e($p->rt); ?>/<?php echo e($p->rw); ?></span>
                                </td>
                                <td><?php echo e($p->dusun); ?></td>
                                <td class="text-right text-nowrap">
                                    <a href="<?php echo e(route('penduduk.show', $p)); ?>" class="action-btn btn-view" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('penduduk.edit', $p)); ?>" class="action-btn btn-edit" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="<?php echo e(route('penduduk.destroy', $p)); ?>" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus data <?php echo e($p->nama_lengkap); ?>?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="action-btn btn-delete" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr class="empty-state">
                                <td colspan="6">
                                    <i class="fas fa-users-slash"></i>
                                    <div>Tidak ada data penduduk ditemukan.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="pagination-info">
                    Menampilkan <?php echo e($penduduks->firstItem() ?? 0); ?>–<?php echo e($penduduks->lastItem() ?? 0); ?> dari
                    <?php echo e($penduduks->total()); ?> data
                </span>
                <?php echo e($penduduks->links('pagination::bootstrap-4')); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\AhsanDz\Claude\Projects\Surat_menyurat_Ngadri\resources\views/penduduk/index.blade.php ENDPATH**/ ?>