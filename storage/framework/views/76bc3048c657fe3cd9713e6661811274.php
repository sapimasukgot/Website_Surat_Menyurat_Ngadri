
<?php $__env->startSection('title', 'Jenis Surat'); ?>

<?php $__env->startSection('content'); ?>
    <div class="feature-card">
        
        <div class="feature-card-header">
            <h3 class="feature-card-title">
                <i class="fas fa-layer-group"></i> Daftar Jenis Surat
            </h3>
            <a href="<?php echo e(route('jenis-surat.create')); ?>" class="header-btn btn-add">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>

        <div class="feature-card-body">
            
            <form method="GET">
                <div class="filter-bar">
                    <div class="filter-group">
                        <label>Cari Jenis Surat</label>
                        <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control"
                            placeholder="Nama surat / kode...">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-filter"><i class="fas fa-search"></i> Cari</button>
                        <a href="<?php echo e(route('jenis-surat.index')); ?>" class="btn btn-reset">Reset</a>
                    </div>
                </div>
            </form>

            
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Surat</th>
                            <th>Field Tambahan</th>
                            <th>Template</th>
                            <th>Dipakai</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $jenisSurats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $js): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <span class="badge-modern badge-blue"><?php echo e($js->kode_surat); ?></span>
                                </td>
                                <td>
                                    <span class="name-primary"><?php echo e($js->nama_surat); ?></span>
                                    <?php if($js->deskripsi): ?>
                                        <div class="subtext"><?php echo e(Str::limit($js->deskripsi, 60)); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge-modern badge-gray">
                                        <i class="fas fa-list-ul"></i> <?php echo e(count($js->fields ?? [])); ?> field
                                    </span>
                                </td>
                                <td>
                                    <?php if($js->hasTemplate()): ?>
                                        <a href="<?php echo e(route('jenis-surat.template', $js)); ?>" class="badge-modern badge-green"
                                            style="text-decoration:none;">
                                            <i class="fas fa-file-word"></i> Ada
                                        </a>
                                    <?php else: ?>
                                        <span class="badge-modern badge-gold">
                                            <i class="fas fa-clock"></i> Belum ada
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="font-weight-600" style="color:#2D3748;"><?php echo e($js->surats_count); ?></span>
                                </td>
                                <td>
                                    <?php if($js->is_active): ?>
                                        <span class="badge-modern badge-green"><span class="status-dot dot-green"></span>
                                            Aktif</span>
                                    <?php else: ?>
                                        <span class="badge-modern badge-gray"><span class="status-dot"
                                                style="background:#CBD5E0;"></span> Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right text-nowrap">
                                    <a href="<?php echo e(route('jenis-surat.edit', $js)); ?>" class="action-btn btn-edit" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="<?php echo e(route('jenis-surat.destroy', $js)); ?>" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus jenis surat ini?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="action-btn btn-delete" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr class="empty-state">
                                <td colspan="7">
                                    <i class="fas fa-folder-open"></i>
                                    <div>Belum ada jenis surat yang dibuat.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div class="mt-3">
                <?php echo e($jenisSurats->links('pagination::bootstrap-4')); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/jenis_surat/index.blade.php ENDPATH**/ ?>