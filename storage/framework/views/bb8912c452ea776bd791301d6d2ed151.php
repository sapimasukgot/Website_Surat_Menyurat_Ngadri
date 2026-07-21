
<?php $__env->startSection('title', 'Sunting Surat'); ?>

<?php ($longFields = ['alamat', 'keterangan_tambahan', 'alamat_usaha']); ?>
<?php ($reserved = ['penandatangan', 'jabatan_ttd', 'penandatangan_role']); ?>
<?php ($currentRole = $surat->data_surat['penandatangan_role'] ?? 'kepala_desa'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="feature-card">
                <div class="feature-card-header">
                    <h3 class="feature-card-title">
                        <i class="fas fa-file-signature"></i> Sunting Isi Surat
                    </h3>
                    <span class="badge-modern badge-blue"><?php echo e($surat->jenisSurat->nama_surat ?? ''); ?></span>
                </div>
                <form action="<?php echo e(route('surat.update', $surat)); ?>" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="feature-card-body">
                        <div class="form-section-title"><i class="fas fa-hashtag"></i> Identitas Surat</div>
                        <div class="form-row">
                            <div class="form-group col-md-7">
                                <label>Nomor Surat</label>
                                <input type="text" name="nomor_surat"
                                    class="form-control <?php $__errorArgs = ['nomor_surat'];
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

                        <?php if(count($surat->data_surat)): ?>
                            <div class="dynamic-fields-box">
                                <h6><i class="fas fa-database mr-1"></i>Isi Surat (dapat disunting)</h6>
                                <div class="form-row">
                                    <?php $__currentLoopData = $surat->data_surat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(in_array($key, $reserved)) continue; ?>
                                        <div class="form-group <?php echo e(in_array($key, $longFields) ? 'col-12' : 'col-md-6'); ?>">
                                            <label style="font-size:0.84rem;font-weight:700;color:#4A5568;">
                                                <?php echo e(\Illuminate\Support\Str::title(str_replace('_', ' ', $key))); ?>

                                            </label>
                                            <?php if(in_array($key, $longFields)): ?>
                                                <textarea name="data[<?php echo e($key); ?>]" rows="2"
                                                    class="form-control"><?php echo e(old('data.' . $key, $value)); ?></textarea>
                                            <?php else: ?>
                                                <input type="text" name="data[<?php echo e($key); ?>]" class="form-control"
                                                    value="<?php echo e(old('data.' . $key, $value)); ?>">
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-section-title"><i class="fas fa-user-tie"></i> Penandatangan</div>
                        <div class="form-group">
                            <label>Ditandatangani oleh <span class="text-danger">*</span></label>
                            <select name="penandatangan_role" class="form-control">
                                <option value="kepala_desa" <?php if(old('penandatangan_role', $currentRole) === 'kepala_desa'): echo 'selected'; endif; ?>>
                                    Kepala Desa — <?php echo e($penandatanganList['kepala_desa'] ?: 'belum diatur'); ?>

                                </option>
                                <option value="sekretaris_desa" <?php if(old('penandatangan_role', $currentRole) === 'sekretaris_desa'): echo 'selected'; endif; ?>>
                                    Sekretaris Desa — <?php echo e($penandatanganList['sekretaris_desa'] ?: 'belum diatur'); ?>

                                </option>
                            </select>
                            <small class="text-muted" style="font-size:0.8rem;">Nama penandatangan mengikuti pengaturan
                                perangkat desa terbaru saat disimpan.</small>
                        </div>

                    </div>
                    <div class="feature-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                        <a href="<?php echo e(route('surat.show', $surat)); ?>" class="btn btn-reset">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="info-card mb-3">
                <h6><i class="fas fa-info-circle mr-1"></i> Catatan</h6>
                <p>Isi yang Anda simpan menjadi <strong>isi final</strong> surat. Bila template diubah di kemudian hari,
                    surat ini tetap memakai data yang tersimpan di sini.</p>
            </div>
            <div class="feature-card" style="margin-bottom:0;">
                <div class="feature-card-header">
                    <h3 class="feature-card-title"><i class="fas fa-user"></i> Data Pemohon</h3>
                </div>
                <div class="feature-card-body" style="padding: 1rem 1.5rem;">
                    <p class="mb-1" style="font-size:0.9rem;"><span class="text-muted">Nama:</span>
                        <strong><?php echo e($surat->penduduk->nama_lengkap ?? '-'); ?></strong></p>
                    <p class="mb-1" style="font-size:0.9rem;"><span class="text-muted">NIK:</span> <span
                            class="nik-code"><?php echo e($surat->penduduk->nik ?? '-'); ?></span></p>
                    <p class="mb-0" style="font-size:0.9rem;"><span class="text-muted">Admin:</span>
                        <?php echo e($surat->user->name ?? '-'); ?></p>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\AhsanDz\Claude\Projects\Surat_menyurat_Ngadri\resources\views/surat/edit.blade.php ENDPATH**/ ?>