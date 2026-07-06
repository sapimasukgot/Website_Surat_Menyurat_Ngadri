
<?php $__env->startSection('title', 'Edit Jenis Surat'); ?>

<?php $__env->startSection('content'); ?>
<div class="card card-primary card-outline">
    <div class="card-header"><h3 class="card-title">Edit Jenis Surat</h3></div>
    <form action="<?php echo e(route('jenis-surat.update', $jenisSurat)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo method_field('PUT'); ?>
        <div class="card-body"><?php echo $__env->make('jenis_surat._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
        <div class="card-footer">
            <button class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
            <a href="<?php echo e(route('jenis-surat.index')); ?>" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/jenis_surat/edit.blade.php ENDPATH**/ ?>