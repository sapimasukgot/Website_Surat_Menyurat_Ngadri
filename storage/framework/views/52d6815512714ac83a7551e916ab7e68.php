
<?php $__env->startSection('title', 'Tambah Penduduk'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-9">
            <div class="feature-card">
                <div class="feature-card-header">
                    <h3 class="feature-card-title">
                        <i class="fas fa-user-plus"></i> Tambah Data Penduduk
                    </h3>
                </div>
                <form action="<?php echo e(route('penduduk.store')); ?>" method="POST">
                    <div class="feature-card-body">
                        <?php echo $__env->make('penduduk._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                    <div class="feature-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Data
                        </button>
                        <a href="<?php echo e(route('penduduk.index')); ?>" class="btn btn-reset">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="info-card">
                <h6><i class="fas fa-info-circle mr-1"></i> Panduan</h6>
                <p>Field bertanda <strong style="color:var(--brand-danger)">*</strong> wajib diisi. NIK dan No. KK harus
                    terdiri dari 16 digit angka.</p>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\AhsanDz\Claude\Projects\Surat_menyurat_Ngadri\resources\views/penduduk/create.blade.php ENDPATH**/ ?>