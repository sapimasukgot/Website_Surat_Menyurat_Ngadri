
<?php $__env->startSection('title', 'Riwayat Surat'); ?>

<?php
    function sortLinkSurat($column, $label, $sort, $direction) {
        $dir = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
        $icon = $sort === $column ? ($direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort';
        $q = array_merge(request()->query(), ['sort' => $column, 'direction' => $dir]);
        return '<a href="'.route('surat.index', $q).'" class="text-dark text-decoration-none">'.$label.' <i class="fas '.$icon.' text-muted small"></i></a>';
    }
?>

<?php $__env->startSection('content'); ?>
<div class="card card-primary card-outline">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title mb-0"><i class="fas fa-history mr-1"></i> Riwayat Surat</h3>
        <a href="<?php echo e(route('surat.create')); ?>" class="btn btn-sm btn-primary ml-auto"><i class="fas fa-plus"></i> Buat Surat</a>
    </div>
    <div class="card-body">
        <form method="GET" class="form-row align-items-end mb-3">
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Pencarian</label>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control form-control-sm" placeholder="Nomor / Nama / NIK">
            </div>
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Jenis Surat</label>
                <select name="jenis_surat_id" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <?php $__currentLoopData = $jenisList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($j->id); ?>" <?php if(request('jenis_surat_id') == $j->id): echo 'selected'; endif; ?>><?php echo e($j->nama_surat); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label class="small mb-1">Dari</label>
                <input type="date" name="dari" value="<?php echo e(request('dari')); ?>" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 mb-2">
                <label class="small mb-1">Sampai</label>
                <input type="date" name="sampai" value="<?php echo e(request('sampai')); ?>" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 mb-2">
                <button class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filter</button>
                <a href="<?php echo e(route('surat.index')); ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="thead-light">
                    <tr>
                        <th><?php echo sortLinkSurat('nomor_surat', 'Nomor Surat', $sort, $direction); ?></th>
                        <th>Jenis</th>
                        <th>Penduduk</th>
                        <th><?php echo sortLinkSurat('tanggal_surat', 'Tanggal', $sort, $direction); ?></th>
                        <th>Admin</th>
                        <th>Berkas</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $surats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><a href="<?php echo e(route('surat.show', $s)); ?>"><?php echo e($s->nomor_surat); ?></a></td>
                            <td><span class="badge badge-secondary"><?php echo e($s->jenisSurat->kode_surat ?? '-'); ?></span></td>
                            <td><?php echo e($s->penduduk->nama_lengkap ?? '-'); ?><div class="small text-muted"><?php echo e($s->penduduk->nik ?? ''); ?></div></td>
                            <td><?php echo e($s->tanggal_surat?->format('d/m/Y')); ?></td>
                            <td><?php echo e($s->user->name ?? '-'); ?></td>
                            <td><?php if($s->hasFile()): ?><span class="badge badge-success">Ada</span><?php else: ?><span class="badge badge-warning">Draft</span><?php endif; ?></td>
                            <td class="text-right text-nowrap">
                                <a href="<?php echo e(route('surat.show', $s)); ?>" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('surat.edit', $s)); ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                <a href="<?php echo e(route('surat.download', $s)); ?>" class="btn btn-xs btn-success"><i class="fas fa-download"></i></a>
                                <form action="<?php echo e(route('surat.destroy', $s)); ?>" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus surat <?php echo e($s->nomor_surat); ?>?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada surat.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">Total <?php echo e($surats->total()); ?> surat</small>
            <?php echo e($surats->links('pagination::bootstrap-4')); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/surat/index.blade.php ENDPATH**/ ?>