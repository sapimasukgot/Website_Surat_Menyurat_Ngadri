
<?php $__env->startSection('title', 'Edit Penduduk'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-9">
            <div class="feature-card">
                <div class="feature-card-header">
                    <h3 class="feature-card-title">
                        <i class="fas fa-user-edit"></i> Edit Data Penduduk
                    </h3>
                    <span class="nik-code"><?php echo e($penduduk->nik); ?></span>
                </div>
                <form action="<?php echo e(route('penduduk.update', $penduduk)); ?>" method="POST">
                    <?php echo method_field('PUT'); ?>
                    <div class="feature-card-body">
                        <?php echo $__env->make('penduduk._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                    <div class="feature-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Perbarui Data
                        </button>
                        <a href="<?php echo e(route('penduduk.index')); ?>" class="btn btn-reset">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="info-card">
                <h6><i class="fas fa-user mr-1"></i> <?php echo e($penduduk->nama_lengkap); ?></h6>
                <p class="mb-1">NIK: <strong><?php echo e($penduduk->nik); ?></strong></p>
                <p class="mb-0">Dusun: <strong><?php echo e($penduduk->dusun ?? '-'); ?></strong></p>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/penduduk/edit.blade.php ENDPATH**/ ?>