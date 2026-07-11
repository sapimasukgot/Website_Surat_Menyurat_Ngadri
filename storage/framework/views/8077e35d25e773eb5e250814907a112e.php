
<?php $__env->startSection('title', 'Tambah Jenis Surat'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-9">
            <div class="feature-card">
                <div class="feature-card-header">
                    <h3 class="feature-card-title">
                        <i class="fas fa-layer-group"></i> Tambah Jenis Surat
                    </h3>
                </div>
                <form action="<?php echo e(route('jenis-surat.store')); ?>" method="POST" enctype="multipart/form-data">
                    <div class="feature-card-body">
                        <?php echo $__env->make('jenis_surat._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                    <div class="feature-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <a href="<?php echo e(route('jenis-surat.index')); ?>" class="btn btn-reset">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="info-card">
                <h6><i class="fas fa-lightbulb mr-1"></i> Tips Placeholder</h6>
                <p>Di template Word, gunakan placeholder seperti: <code>${nomor_surat}</code>, <code>${nama}</code>,
                    <code>${nik}</code>, <code>${alamat}</code>, <code>${tanggal}</code>.</p>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/jenis_surat/create.blade.php ENDPATH**/ ?>