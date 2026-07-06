
<?php $__env->startSection('title', 'Buat Surat'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="feature-card">
                <div class="feature-card-header">
                    <h3 class="feature-card-title">
                        <i class="fas fa-file-signature"></i> Buat Surat Baru
                    </h3>
                </div>
                <form action="<?php echo e(route('surat.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="feature-card-body">
                        <div class="form-section-title"><i class="fas fa-cog"></i> Konfigurasi Surat</div>
                        <div class="form-row">
                            <div class="form-group col-md-7">
                                <label>Jenis Surat <span class="text-danger">*</span></label>
                                <select name="jenis_surat_id" id="jenis_select"
                                    class="form-control <?php $__errorArgs = ['jenis_surat_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">— Pilih Jenis Surat —</option>
                                    <?php $__currentLoopData = $jenisList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($j->id); ?>" <?php if(old('jenis_surat_id') == $j->id): echo 'selected'; endif; ?>>
                                            <?php echo e($j->nama_surat); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['jenis_surat_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group col-md-5">
                                <label>Tanggal Surat <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_surat" class="form-control"
                                    value="<?php echo e(old('tanggal_surat', date('Y-m-d'))); ?>" required>
                            </div>
                        </div>

                        <div class="form-section-title mt-1"><i class="fas fa-user"></i> Penduduk Pemohon</div>
                        <div class="form-group">
                            <label>Penduduk <span class="text-danger">*</span></label>
                            <select name="penduduk_id"
                                class="form-control tom-select <?php $__errorArgs = ['penduduk_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                data-placeholder="Cari NIK / Nama..." required>
                                <option value="">— Pilih Penduduk —</option>
                                <?php $__currentLoopData = $penduduks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($p->id); ?>" <?php if(old('penduduk_id') == $p->id): echo 'selected'; endif; ?>><?php echo e($p->nik); ?> —
                                        <?php echo e($p->nama_lengkap); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['penduduk_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback d-block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted" style="font-size:0.8rem;">Data pribadi (nama, alamat, RT/RW, dll)
                                otomatis diambil dari database.</small>
                        </div>

                        
                        <div id="additional-fields"></div>

                        <div class="form-section-title"><i class="fas fa-sticky-note"></i> Keterangan</div>
                        <div class="form-group">
                            <label>Keterangan Internal</label>
                            <textarea name="keterangan" rows="2" class="form-control"
                                placeholder="Catatan internal (tidak tampil di surat)..."><?php echo e(old('keterangan')); ?></textarea>
                        </div>
                    </div>
                    <div class="feature-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-arrow-right mr-1"></i> Lanjut (Buat Draft)
                        </button>
                        <a href="<?php echo e(route('surat.index')); ?>" class="btn btn-reset">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="info-card">
                <h6><i class="fas fa-info-circle mr-1"></i> Cara Penggunaan</h6>
                <p>1. Pilih jenis surat dan penduduk pemohon.</p>
                <p>2. Isi field tambahan yang muncul otomatis sesuai jenis surat.</p>
                <p class="mb-0">3. Klik <strong>Lanjut</strong> untuk membuat draft. Setelah itu Anda bisa mengunduh berkas
                    akhirnya.</p>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        const jenisFields = <?php echo json_encode($jenisList->keyBy('id')->map->fields, 15, 512) ?>;
        const oldData = <?php echo json_encode(old('data', []), 512) ?>;
        const container = document.getElementById('additional-fields');
        const select = document.getElementById('jenis_select');

        function renderFields(id) {
            container.innerHTML = '';
            const fields = jenisFields[id] || [];
            if (!fields.length) return;
            const wrap = document.createElement('div');
            wrap.className = 'dynamic-fields-box';
            wrap.innerHTML = '<h6><i class="fas fa-pen mr-1"></i>Data Tambahan Surat</h6>';
            const row = document.createElement('div');
            row.className = 'form-row';
            fields.forEach(f => {
                const val = oldData[f.name] || '';
                const req = f.required ? '<span class="text-danger">*</span>' : '';
                const isLong = f.type === 'textarea';
                const input = isLong
                    ? `<textarea name="data[${f.name}]" rows="2" class="form-control" ${f.required ? 'required' : ''}>${val}</textarea>`
                    : `<input type="${f.type === 'number' ? 'number' : (f.type === 'date' ? 'date' : 'text')}" name="data[${f.name}]" class="form-control" value="${val}" ${f.required ? 'required' : ''}>`;
                const colClass = isLong ? 'col-12' : 'col-md-6';
                row.insertAdjacentHTML('beforeend',
                    `<div class="form-group ${colClass}"><label style="font-size:0.84rem;font-weight:700;color:#4A5568;">${f.label} ${req}</label>${input}</div>`);
            });
            wrap.appendChild(row);
            container.appendChild(wrap);
        }

        select.addEventListener('change', e => renderFields(e.target.value));
        if (select.value) renderFields(select.value);
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/surat/create.blade.php ENDPATH**/ ?>