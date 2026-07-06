
<?php $__env->startSection('title', 'Detail Penduduk'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-7">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex">
                <h3 class="card-title"><?php echo e($penduduk->nama_lengkap); ?></h3>
                <a href="<?php echo e(route('penduduk.edit', $penduduk)); ?>" class="btn btn-xs btn-warning ml-auto"><i class="fas fa-edit"></i> Edit</a>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th width="35%">NIK</th><td><?php echo e($penduduk->nik); ?></td></tr>
                    <tr><th>Nomor KK</th><td><?php echo e($penduduk->no_kk); ?></td></tr>
                    <tr><th>Tempat, Tgl Lahir</th><td><?php echo e($penduduk->tempat_lahir); ?>, <?php echo e($penduduk->tanggal_lahir?->format('d-m-Y')); ?></td></tr>
                    <tr><th>Jenis Kelamin</th><td><?php echo e($penduduk->jenis_kelamin_label); ?></td></tr>
                    <tr><th>Agama</th><td><?php echo e($penduduk->agama); ?></td></tr>
                    <tr><th>Pendidikan</th><td><?php echo e($penduduk->pendidikan); ?></td></tr>
                    <tr><th>Pekerjaan</th><td><?php echo e($penduduk->pekerjaan); ?></td></tr>
                    <tr><th>Status Kawin</th><td><?php echo e($penduduk->status_kawin); ?></td></tr>
                    <tr><th>Alamat</th><td><?php echo e($penduduk->alamat); ?> RT <?php echo e($penduduk->rt); ?>/RW <?php echo e($penduduk->rw); ?>, Dusun <?php echo e($penduduk->dusun); ?></td></tr>
                    <tr><th>No HP</th><td><?php echo e($penduduk->no_hp); ?></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card card-outline card-secondary">
            <div class="card-header"><h3 class="card-title">Riwayat Surat</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Nomor</th><th>Jenis</th><th>Tgl</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $penduduk->surats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr><td><a href="<?php echo e(route('surat.show', $s)); ?>"><?php echo e($s->nomor_surat); ?></a></td>
                                <td><?php echo e($s->jenisSurat->kode_surat ?? '-'); ?></td>
                                <td><?php echo e($s->tanggal_surat?->format('d/m/y')); ?></td></tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada surat.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<a href="<?php echo e(route('penduduk.index')); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/penduduk/show.blade.php ENDPATH**/ ?>