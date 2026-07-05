<?php echo csrf_field(); ?>
<div class="form-row">
    <div class="form-group col-md-6">
        <label>NIK <span class="text-danger">*</span></label>
        <input type="text" name="nik" maxlength="16" class="form-control <?php $__errorArgs = ['nik'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               value="<?php echo e(old('nik', $penduduk->nik)); ?>" required>
        <?php $__errorArgs = ['nik'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="form-group col-md-6">
        <label>Nomor KK <span class="text-danger">*</span></label>
        <input type="text" name="no_kk" maxlength="16" class="form-control <?php $__errorArgs = ['no_kk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               value="<?php echo e(old('no_kk', $penduduk->no_kk)); ?>" required>
        <?php $__errorArgs = ['no_kk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<div class="form-group">
    <label>Nama Lengkap <span class="text-danger">*</span></label>
    <input type="text" name="nama_lengkap" class="form-control <?php $__errorArgs = ['nama_lengkap'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
           value="<?php echo e(old('nama_lengkap', $penduduk->nama_lengkap)); ?>" required>
    <?php $__errorArgs = ['nama_lengkap'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>Tempat Lahir</label>
        <input type="text" name="tempat_lahir" class="form-control" value="<?php echo e(old('tempat_lahir', $penduduk->tempat_lahir)); ?>">
    </div>
    <div class="form-group col-md-3">
        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" class="form-control"
               value="<?php echo e(old('tanggal_lahir', $penduduk->tanggal_lahir?->format('Y-m-d'))); ?>">
    </div>
    <div class="form-group col-md-3">
        <label>Jenis Kelamin <span class="text-danger">*</span></label>
        <select name="jenis_kelamin" class="form-control" required>
            <?php $__currentLoopData = \App\Models\Penduduk::JENIS_KELAMIN; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($k); ?>" <?php if(old('jenis_kelamin', $penduduk->jenis_kelamin) === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-3">
        <label>Agama</label>
        <select name="agama" class="form-control">
            <option value="">-</option>
            <?php $__currentLoopData = \App\Models\Penduduk::AGAMA; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($a); ?>" <?php if(old('agama', $penduduk->agama) === $a): echo 'selected'; endif; ?>><?php echo e($a); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="form-group col-md-3">
        <label>Pendidikan</label>
        <input type="text" name="pendidikan" class="form-control" value="<?php echo e(old('pendidikan', $penduduk->pendidikan)); ?>">
    </div>
    <div class="form-group col-md-3">
        <label>Pekerjaan</label>
        <input type="text" name="pekerjaan" class="form-control" value="<?php echo e(old('pekerjaan', $penduduk->pekerjaan)); ?>">
    </div>
    <div class="form-group col-md-3">
        <label>Status Kawin <span class="text-danger">*</span></label>
        <select name="status_kawin" class="form-control" required>
            <?php $__currentLoopData = \App\Models\Penduduk::STATUS_KAWIN; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($s); ?>" <?php if(old('status_kawin', $penduduk->status_kawin) === $s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label>Alamat <span class="text-danger">*</span></label>
    <textarea name="alamat" rows="2" class="form-control <?php $__errorArgs = ['alamat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('alamat', $penduduk->alamat)); ?></textarea>
    <?php $__errorArgs = ['alamat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<div class="form-row">
    <div class="form-group col-md-2">
        <label>RT</label>
        <input type="text" name="rt" maxlength="3" class="form-control" value="<?php echo e(old('rt', $penduduk->rt)); ?>">
    </div>
    <div class="form-group col-md-2">
        <label>RW</label>
        <input type="text" name="rw" maxlength="3" class="form-control" value="<?php echo e(old('rw', $penduduk->rw)); ?>">
    </div>
    <div class="form-group col-md-4">
        <label>Dusun</label>
        <input type="text" name="dusun" class="form-control" value="<?php echo e(old('dusun', $penduduk->dusun)); ?>">
    </div>
    <div class="form-group col-md-4">
        <label>Nomor HP</label>
        <input type="text" name="no_hp" class="form-control" value="<?php echo e(old('no_hp', $penduduk->no_hp)); ?>">
    </div>
</div>
<?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/penduduk/_form.blade.php ENDPATH**/ ?>