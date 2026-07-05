
<?php $__env->startSection('title', 'Import Data Penduduk'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-file-import mr-1"></i> Unggah Berkas Excel</h3></div>
            <form action="<?php echo e(route('penduduk.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="card-body">
                    <div class="form-group">
                        <label>Berkas (.xlsx / .xls, maks 5 MB)</label>
                        <div class="custom-file">
                            <input type="file" name="file" id="file" class="custom-file-input <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept=".xlsx,.xls" required>
                            <label class="custom-file-label" for="file">Pilih berkas...</label>
                            <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="alert alert-info small mb-0">
                        <strong>Aturan import:</strong> NIK baru akan <b>ditambahkan</b>, NIK yang sudah ada akan <b>diperbarui</b> (tanpa duplikat).
                        Kolom yang diharapkan: <code>NIK, No KK, Nama Lengkap, Tempat Lahir, Tanggal Lahir, Jenis Kelamin, Agama, Pendidikan, Pekerjaan, Status Kawin, Alamat, RT, RW, Dusun, No HP</code>.
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary"><i class="fas fa-upload"></i> Import</button>
                    <a href="<?php echo e(route('penduduk.index')); ?>" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <?php if($lastLog): ?>
            <div class="card card-outline card-secondary">
                <div class="card-header"><h3 class="card-title">Hasil Import Terakhir</h3></div>
                <div class="card-body">
                    <p class="mb-1"><b>Berkas:</b> <?php echo e($lastLog->file_name); ?></p>
                    <p class="mb-2 text-muted small"><?php echo e($lastLog->created_at->format('d/m/Y H:i')); ?> oleh <?php echo e($lastLog->user->name ?? '-'); ?></p>
                    <div class="row text-center">
                        <div class="col"><span class="badge badge-success p-2">Baru: <?php echo e($lastLog->inserted_count); ?></span></div>
                        <div class="col"><span class="badge badge-info p-2">Diperbarui: <?php echo e($lastLog->updated_count); ?></span></div>
                        <div class="col"><span class="badge badge-danger p-2">Gagal: <?php echo e($lastLog->failed_count); ?></span></div>
                    </div>
                    <?php if(!empty($lastLog->errors)): ?>
                        <hr>
                        <h6>Detail Error</h6>
                        <div class="table-responsive" style="max-height:240px;overflow:auto">
                            <table class="table table-sm">
                                <thead><tr><th>Baris</th><th>NIK</th><th>Pesan</th></tr></thead>
                                <tbody>
                                    <?php $__currentLoopData = $lastLog->errors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr><td><?php echo e($err['baris']); ?></td><td><?php echo e($err['nik']); ?></td><td class="small"><?php echo e($err['pesan']); ?></td></tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.getElementById('file').addEventListener('change', function (e) {
        const name = e.target.files[0]?.name || 'Pilih berkas...';
        e.target.nextElementSibling.textContent = name;
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/penduduk/import.blade.php ENDPATH**/ ?>