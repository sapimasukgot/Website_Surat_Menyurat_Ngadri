
<?php $__env->startSection('title', 'Detail Surat'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><?php echo e($surat->nomor_surat); ?></h3>
                <span class="badge badge-info float-right"><?php echo e($surat->jenisSurat->nama_surat ?? ''); ?></span>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th width="30%">Tanggal Surat</th><td><?php echo e($surat->tanggal_surat?->format('d F Y')); ?></td></tr>
                    <tr><th>Penduduk</th><td><?php echo e($surat->penduduk->nama_lengkap ?? '-'); ?> (<?php echo e($surat->penduduk->nik ?? '-'); ?>)</td></tr>
                    <tr><th>Dibuat oleh</th><td><?php echo e($surat->user->name ?? '-'); ?></td></tr>
                </table>
                <h6 class="text-primary"><i class="fas fa-file-alt mr-1"></i> Isi Surat</h6>
                <table class="table table-sm table-bordered">
                    <?php $__currentLoopData = $surat->data_surat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr><th width="30%"><?php echo e(\Illuminate\Support\Str::title(str_replace('_', ' ', $key))); ?></th><td><?php echo e(is_array($value) ? implode(', ', $value) : $value); ?></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Tindakan</h3></div>
            <div class="card-body">
                <a href="<?php echo e(route('surat.edit', $surat)); ?>" class="btn btn-warning btn-block"><i class="fas fa-edit"></i> Sunting Isi</a>

                <form action="<?php echo e(route('surat.generate', $surat)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button class="btn btn-primary btn-block"><i class="fas fa-file-word"></i> Simpan &amp; Buat Surat (.docx)</button>
                </form>

                <?php if($surat->hasFile()): ?>
                    <hr>
                    <a href="<?php echo e(route('surat.download', $surat)); ?>" class="btn btn-success btn-block"><i class="fas fa-download"></i> Unduh .docx</a>
                    <a href="<?php echo e(route('surat.print', $surat)); ?>" class="btn btn-outline-primary btn-block"><i class="fas fa-print"></i> Cetak Ulang (regenerasi)</a>
                    <p class="small text-muted mt-2 mb-0">Berkas siap dibuka di Microsoft Word untuk dicetak.</p>
                <?php else: ?>
                    <p class="small text-muted mt-2">Berkas .docx belum dibuat. Klik tombol di atas untuk menghasilkannya.</p>
                <?php endif; ?>

                <hr>
                <form action="<?php echo e(route('surat.destroy', $surat)); ?>" method="POST" onsubmit="return confirm('Hapus surat ini?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-outline-danger btn-block"><i class="fas fa-trash"></i> Hapus Surat</button>
                </form>
            </div>
        </div>
    </div>
</div>
<a href="<?php echo e(route('surat.index')); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Riwayat Surat</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/surat/show.blade.php ENDPATH**/ ?>