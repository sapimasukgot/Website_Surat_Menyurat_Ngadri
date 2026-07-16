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
                <?php ($reserved = ['penandatangan', 'jabatan_ttd', 'penandatangan_role']); ?>
                <table class="table table-sm table-bordered">
                    <?php $__currentLoopData = $surat->data_surat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(in_array($key, $reserved)) continue; ?>
                        <tr><th width="30%"><?php echo e(\Illuminate\Support\Str::title(str_replace('_', ' ', $key))); ?></th><td><?php echo e(is_array($value) ? implode(', ', $value) : $value); ?></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr class="table-light">
                        <th width="30%"><i class="fas fa-user-tie mr-1"></i> Penandatangan</th>
                        <td><?php echo e($surat->data('penandatangan', '-')); ?> <span class="text-muted">(<?php echo e($surat->data('jabatan_ttd', 'Kepala Desa')); ?>)</span></td>
                    </tr>
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
                    <a href="<?php echo e(route('surat.print', $surat)); ?>" target="_blank" class="btn btn-primary btn-block"><i class="fas fa-print"></i> Print (cetak langsung)</a>
                    <a href="<?php echo e(route('surat.download', $surat)); ?>" class="btn btn-success btn-block"><i class="fas fa-file-word"></i> Unduh .docx</a>
                    <p class="small text-muted mt-2 mb-0">Print membuka dialog cetak langsung di browser. .docx untuk diedit di Microsoft Word.</p>
                <?php else: ?>
                    <p class="small text-muted mt-2">Berkas belum dibuat. Klik tombol di atas untuk menghasilkannya.</p>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\AhsanDz\Claude\Projects\Surat_menyurat_Ngadri\resources\views/surat/show.blade.php ENDPATH**/ ?>