<?php echo csrf_field(); ?>
<div class="form-section-title"><i class="fas fa-file-alt"></i> Informasi Dasar Surat</div>
<div class="form-row">
    <div class="form-group col-md-8">
        <label>Nama Surat <span class="text-danger">*</span></label>
        <input type="text" name="nama_surat" class="form-control <?php $__errorArgs = ['nama_surat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('nama_surat', $jenisSurat->nama_surat)); ?>" required>
        <?php $__errorArgs = ['nama_surat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="form-group col-md-4">
        <label>Kode Surat <span class="text-danger">*</span></label>
        <input type="text" name="kode_surat"
            class="form-control text-uppercase <?php $__errorArgs = ['kode_surat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('kode_surat', $jenisSurat->kode_surat)); ?>" required>
        <?php $__errorArgs = ['kode_surat'];
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
    <label>Deskripsi</label>
    <textarea name="deskripsi" rows="2" class="form-control"
        placeholder="Keterangan singkat tentang jenis surat ini..."><?php echo e(old('deskripsi', $jenisSurat->deskripsi)); ?></textarea>
</div>

<div class="form-section-title mt-2"><i class="fas fa-file-word"></i> Template Dokumen</div>
<div class="form-group">
    <label>Template Surat (.docx)</label>
    <div class="custom-file">
        <input type="file" name="template" id="template"
            class="custom-file-input <?php $__errorArgs = ['template'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept=".docx">
        <label class="custom-file-label" for="template">
            <?php echo e($jenisSurat->template_original_name ?? 'Pilih berkas .docx...'); ?>

        </label>
        <?php $__errorArgs = ['template'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <?php if($jenisSurat->hasTemplate()): ?>
        <small class="text-muted">Template saat ini: <a
                href="<?php echo e(route('jenis-surat.template', $jenisSurat)); ?>"><?php echo e($jenisSurat->template_original_name); ?></a>.
            Unggah berkas baru untuk mengganti.</small>
    <?php endif; ?>
    <small class="form-text text-muted">Gunakan placeholder seperti <code>${nomor_surat}</code>, <code>${nama}</code>,
        <code>${nik}</code>, <code>${alamat}</code>, dll di dalam dokumen Word.</small>
</div>

<div class="form-section-title mt-2"><i class="fas fa-list-ul"></i> Field Tambahan (Dinamis)</div>
<p class="text-muted" style="font-size:0.84rem;">Field ini akan tampil saat pembuatan surat. Placeholder otomatis dari
    label (mis. "Nama Usaha" &rarr; <code>${nama_usaha}</code>).</p>

<table class="table table-sm" id="fields-table">
    <thead>
        <tr>
            <th style="width:40%">Label</th>
            <th style="width:25%">Tipe</th>
            <th style="width:20%">Wajib</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php ($rows = old('fields', $jenisSurat->fields ?? [])); ?>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><input type="text" name="fields[<?php echo e($i); ?>][label]" class="form-control form-control-sm"
                        value="<?php echo e($f['label'] ?? ''); ?>"></td>
                <td>
                    <select name="fields[<?php echo e($i); ?>][type]" class="form-control form-control-sm">
                        <?php $__currentLoopData = ['text' => 'Teks', 'textarea' => 'Teks Panjang', 'date' => 'Tanggal', 'number' => 'Angka']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php if(($f['type'] ?? 'text') === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </td>
                <td><input type="checkbox" name="fields[<?php echo e($i); ?>][required]" value="1" <?php if($f['required'] ?? false): echo 'checked'; endif; ?>>
                </td>
                <td><button type="button" class="btn btn-xs btn-danger" onclick="this.closest('tr').remove()"><i
                            class="fas fa-times"></i></button></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <?php endif; ?>
    </tbody>
</table>
<button type="button" class="header-btn btn-import mt-2" id="add-field" style="font-size:0.82rem;"><i
        class="fas fa-plus"></i> Tambah Field</button>

<div class="form-section-title mt-3"><i class="fas fa-toggle-on"></i> Status</div>
<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
            <?php if(old('is_active', $jenisSurat->is_active ?? true)): echo 'checked'; endif; ?>>
        <label class="custom-control-label" for="is_active"
            style="font-size:0.9rem;font-weight:600;color:#4A5568;">Jenis surat ini aktif dan bisa digunakan</label>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <script>
        let idx = <?php echo e(count($rows)); ?>;
        document.getElementById('add-field').addEventListener('click', function () {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="fields[${idx}][label]" class="form-control form-control-sm"></td>
                <td><select name="fields[${idx}][type]" class="form-control form-control-sm">
                    <option value="text">Teks</option><option value="textarea">Teks Panjang</option>
                    <option value="date">Tanggal</option><option value="number">Angka</option></select></td>
                <td><input type="checkbox" name="fields[${idx}][required]" value="1"></td>
                <td><button type="button" class="btn btn-xs btn-danger" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td>`;
            document.querySelector('#fields-table tbody').appendChild(tr);
            idx++;
        });
        const tpl = document.getElementById('template');
        if (tpl) tpl.addEventListener('change', e => e.target.nextElementSibling.textContent = e.target.files[0]?.name || 'Pilih berkas .docx...');
    </script>
<?php $__env->stopPush(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/jenis_surat/_form.blade.php ENDPATH**/ ?>