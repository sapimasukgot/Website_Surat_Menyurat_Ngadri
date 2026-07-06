
<?php $__env->startSection('title', 'Sunting Surat'); ?>

<?php ($longFields = ['alamat', 'keterangan_tambahan', 'alamat_usaha']); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Sunting Isi Surat</h3>
                <span class="badge badge-info float-right"><?php echo e($surat->jenisSurat->nama_surat ?? ''); ?></span>
            </div>
            <form action="<?php echo e(route('surat.update', $surat)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <label>Nomor Surat</label>
                            <input type="text" name="nomor_surat" class="form-control <?php $__errorArgs = ['nomor_surat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('nomor_surat', $surat->nomor_surat)); ?>" required>
                            <?php $__errorArgs = ['nomor_surat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="form-group col-md-5">
                            <label>Tanggal Surat</label>
                            <input type="date" name="tanggal_surat" class="form-control"
                                   value="<?php echo e(old('tanggal_surat', $surat->tanggal_surat?->format('Y-m-d'))); ?>" required>
                        </div>
                    </div>

                    <h6 class="text-primary mt-2"><i class="fas fa-database mr-1"></i> Isi Surat (dapat disunting)</h6>
                    <div class="form-row">
                        <?php $__currentLoopData = $surat->data_surat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-group <?php echo e(in_array($key, $longFields) ? 'col-12' : 'col-md-6'); ?>">
                                <label class="mb-1"><?php echo e(\Illuminate\Support\Str::title(str_replace('_', ' ', $key))); ?></label>
                                <?php if(in_array($key, $longFields)): ?>
                                    <textarea name="data[<?php echo e($key); ?>]" rows="2" class="form-control"><?php echo e(old('data.'.$key, $value)); ?></textarea>
                                <?php else: ?>
                                    <input type="text" name="data[<?php echo e($key); ?>]" class="form-control" value="<?php echo e(old('data.'.$key, $value)); ?>">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="form-group">
                        <label>Keterangan (internal)</label>
                        <textarea name="keterangan" rows="2" class="form-control"><?php echo e(old('keterangan', $surat->keterangan)); ?></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    <a href="<?php echo e(route('surat.show', $surat)); ?>" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="alert alert-info">
            <h6><i class="fas fa-info-circle mr-1"></i> Catatan</h6>
            <p class="small mb-0">Isi yang Anda simpan menjadi <b>isi final</b> surat. Bila template diubah di kemudian hari, surat ini tetap memakai data yang tersimpan di sini.</p>
        </div>
        <div class="card">
            <div class="card-body">
                <p class="mb-1"><b>Penduduk:</b> <?php echo e($surat->penduduk->nama_lengkap ?? '-'); ?></p>
                <p class="mb-1"><b>NIK:</b> <?php echo e($surat->penduduk->nik ?? '-'); ?></p>
                <p class="mb-0"><b>Dibuat oleh:</b> <?php echo e($surat->user->name ?? '-'); ?></p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/surat/edit.blade.php ENDPATH**/ ?>