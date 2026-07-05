
<?php $__env->startSection('title', 'Jenis Surat'); ?>

<?php $__env->startSection('content'); ?>
<div class="card card-primary card-outline">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title mb-0"><i class="fas fa-layer-group mr-1"></i> Daftar Jenis Surat</h3>
        <a href="<?php echo e(route('jenis-surat.create')); ?>" class="btn btn-sm btn-primary ml-auto"><i class="fas fa-plus"></i> Tambah</a>
    </div>
    <div class="card-body">
        <form method="GET" class="form-inline mb-3">
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control form-control-sm mr-2" placeholder="Cari nama / kode">
            <button class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr><th>Kode</th><th>Nama Surat</th><th>Field Tambahan</th><th>Template</th><th>Dipakai</th><th>Status</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $jenisSurats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $js): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><span class="badge badge-secondary"><?php echo e($js->kode_surat); ?></span></td>
                            <td><?php echo e($js->nama_surat); ?><div class="small text-muted"><?php echo e(Str::limit($js->deskripsi, 60)); ?></div></td>
                            <td><?php echo e(count($js->fields ?? [])); ?> field</td>
                            <td>
                                <?php if($js->hasTemplate()): ?>
                                    <a href="<?php echo e(route('jenis-surat.template', $js)); ?>" class="badge badge-success"><i class="fas fa-file-word"></i> Ada</a>
                                <?php else: ?>
                                    <span class="badge badge-warning">Belum ada</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($js->surats_count); ?></td>
                            <td><?php if($js->is_active): ?><span class="badge badge-primary">Aktif</span><?php else: ?><span class="badge badge-secondary">Nonaktif</span><?php endif; ?></td>
                            <td class="text-right text-nowrap">
                                <a href="<?php echo e(route('jenis-surat.edit', $js)); ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="<?php echo e(route('jenis-surat.destroy', $js)); ?>" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus jenis surat ini?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada jenis surat.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($jenisSurats->links('pagination::bootstrap-4')); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/jenis_surat/index.blade.php ENDPATH**/ ?>