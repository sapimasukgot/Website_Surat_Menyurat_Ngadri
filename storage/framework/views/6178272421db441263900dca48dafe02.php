
<?php $__env->startSection('title', 'Edit Jenis Surat'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-9">
            <div class="feature-card">
                <div class="feature-card-header">
                    <h3 class="feature-card-title">
                        <i class="fas fa-layer-group"></i> Edit Jenis Surat
                    </h3>
                    <span class="badge-modern badge-blue"><?php echo e($jenisSurat->kode_surat); ?></span>
                </div>
                <form action="<?php echo e(route('jenis-surat.update', $jenisSurat)); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo method_field('PUT'); ?>
                    <div class="feature-card-body">
                        <?php echo $__env->make('jenis_surat._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                    <div class="feature-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Perbarui
                        </button>
                        <a href="<?php echo e(route('jenis-surat.index')); ?>" class="btn btn-reset">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="info-card">
                <h6><i class="fas fa-chart-bar mr-1"></i> Info</h6>
                <p class="mb-1">Digunakan oleh: <strong><?php echo e($jenisSurat->surats_count); ?> surat</strong></p>
                <p class="mb-0">Status:
                    <?php if($jenisSurat->is_active): ?>
                        <span class="badge-modern badge-green">Aktif</span>
                    <?php else: ?>
                        <span class="badge-modern badge-gray">Nonaktif</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\AhsanDz\Claude\Projects\Surat_menyurat_Ngadri\resources\views/jenis_surat/edit.blade.php ENDPATH**/ ?>