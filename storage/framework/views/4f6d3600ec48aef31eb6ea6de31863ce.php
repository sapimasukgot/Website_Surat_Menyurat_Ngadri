
<?php $__env->startSection('title', 'Data Penduduk'); ?>

<?php
    function sortLink($column, $label, $sort, $direction) {
        $dir = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
        $icon = $sort === $column ? ($direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort';
        $q = array_merge(request()->query(), ['sort' => $column, 'direction' => $dir]);
        return '<a href="'.route('penduduk.index', $q).'" class="text-dark text-decoration-none">'.$label.' <i class="fas '.$icon.' text-muted small"></i></a>';
    }
?>

<?php $__env->startSection('content'); ?>
<div class="card card-primary card-outline">
    <div class="card-header d-flex flex-wrap align-items-center">
        <h3 class="card-title mb-0"><i class="fas fa-users mr-1"></i> Daftar Penduduk</h3>
        <div class="ml-auto">
            <a href="<?php echo e(route('penduduk.create')); ?>" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Tambah</a>
            <a href="<?php echo e(route('penduduk.import.form')); ?>" class="btn btn-sm btn-success"><i class="fas fa-file-import"></i> Import</a>
            <a href="<?php echo e(route('penduduk.export')); ?>" class="btn btn-sm btn-secondary"><i class="fas fa-file-excel"></i> Export</a>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" class="form-row align-items-end mb-3">
            <div class="col-md-4 mb-2">
                <label class="small mb-1">Pencarian</label>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control form-control-sm" placeholder="NIK / No KK / Nama">
            </div>
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Dusun</label>
                <select name="dusun" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <?php $__currentLoopData = $dusunList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($d); ?>" <?php if(request('dusun') === $d): echo 'selected'; endif; ?>><?php echo e($d); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label class="small mb-1">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <option value="L" <?php if(request('jenis_kelamin')==='L'): echo 'selected'; endif; ?>>Laki-laki</option>
                    <option value="P" <?php if(request('jenis_kelamin')==='P'): echo 'selected'; endif; ?>>Perempuan</option>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <button class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Cari</button>
                <a href="<?php echo e(route('penduduk.index')); ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="thead-light">
                    <tr>
                        <th><?php echo sortLink('nik', 'NIK', $sort, $direction); ?></th>
                        <th><?php echo sortLink('nama_lengkap', 'Nama Lengkap', $sort, $direction); ?></th>
                        <th>L/P</th>
                        <th>RT/RW</th>
                        <th><?php echo sortLink('dusun', 'Dusun', $sort, $direction); ?></th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $penduduks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><code><?php echo e($p->nik); ?></code></td>
                            <td><?php echo e($p->nama_lengkap); ?></td>
                            <td><?php echo e($p->jenis_kelamin); ?></td>
                            <td><?php echo e($p->rt); ?>/<?php echo e($p->rw); ?></td>
                            <td><?php echo e($p->dusun); ?></td>
                            <td class="text-right text-nowrap">
                                <a href="<?php echo e(route('penduduk.show', $p)); ?>" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('penduduk.edit', $p)); ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="<?php echo e(route('penduduk.destroy', $p)); ?>" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus data <?php echo e($p->nama_lengkap); ?>?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data penduduk.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">Menampilkan <?php echo e($penduduks->firstItem() ?? 0); ?>-<?php echo e($penduduks->lastItem() ?? 0); ?> dari <?php echo e($penduduks->total()); ?> data</small>
            <?php echo e($penduduks->links('pagination::bootstrap-4')); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/penduduk/index.blade.php ENDPATH**/ ?>