
<?php $__env->startSection('title', 'Riwayat Surat'); ?>

<?php
    function sortLinkSurat($column, $label, $sort, $direction)
    {
        $dir = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
        $icon = $sort === $column ? ($direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort';
        $q = array_merge(request()->query(), ['sort' => $column, 'direction' => $dir]);
        return '<a href="' . route('surat.index', $q) . '" class="text-dark text-decoration-none font-weight-bold">' . $label . ' <i class="fas ' . $icon . ' text-muted small"></i></a>';
    }
?>

<?php $__env->startSection('content'); ?>
    <div class="feature-card">
        
        <div class="feature-card-header">
            <h3 class="feature-card-title">
                <i class="fas fa-envelope-open-text"></i> Riwayat Surat
            </h3>
            <a href="<?php echo e(route('surat.create')); ?>" class="header-btn btn-add">
                <i class="fas fa-plus"></i> Buat Surat
            </a>
        </div>

        <div class="feature-card-body">
            
            <form method="GET">
                <div class="filter-bar">
                    <div class="filter-group">
                        <label>Pencarian</label>
                        <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control"
                            placeholder="Nomor / Nama / NIK">
                    </div>
                    <div class="filter-group">
                        <label>Jenis Surat</label>
                        <select name="jenis_surat_id" class="form-control">
                            <option value="">Semua</option>
                            <?php $__currentLoopData = $jenisList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($j->id); ?>" <?php if(request('jenis_surat_id') == $j->id): echo 'selected'; endif; ?>><?php echo e($j->nama_surat); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="filter-group" style="max-width:155px;">
                        <label>Dari Tanggal</label>
                        <input type="date" name="dari" value="<?php echo e(request('dari')); ?>" class="form-control">
                    </div>
                    <div class="filter-group" style="max-width:155px;">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="sampai" value="<?php echo e(request('sampai')); ?>" class="form-control">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-filter"><i class="fas fa-filter"></i> Filter</button>
                        <a href="<?php echo e(route('surat.index')); ?>" class="btn btn-reset">Reset</a>
                    </div>
                </div>
            </form>

            
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th><?php echo sortLinkSurat('nomor_surat', 'Nomor Surat', $sort, $direction); ?></th>
                            <th>Jenis</th>
                            <th>Penduduk</th>
                            <th><?php echo sortLinkSurat('tanggal_surat', 'Tanggal', $sort, $direction); ?></th>
                            <th>Admin</th>
                            <th>Berkas</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $surats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('surat.show', $s)); ?>" style="font-weight:700;color:var(--brand-primary);">
                                        <?php echo e($s->nomor_surat); ?>

                                    </a>
                                </td>
                                <td>
                                    <span class="badge-modern badge-blue"><?php echo e($s->jenisSurat->kode_surat ?? '-'); ?></span>
                                </td>
                                <td>
                                    <span class="name-primary"><?php echo e($s->penduduk->nama_lengkap ?? '-'); ?></span>
                                    <?php if($s->penduduk->nik ?? false): ?>
                                        <div class="subtext"><?php echo e($s->penduduk->nik); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="white-space:nowrap; font-weight:600;">
                                    <?php echo e($s->tanggal_surat?->format('d/m/Y')); ?>

                                </td>
                                <td><?php echo e($s->user->name ?? '-'); ?></td>
                                <td>
                                    <?php if($s->hasFile()): ?>
                                        <span class="badge-modern badge-green">
                                            <span class="status-dot dot-green"></span> Ada
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-modern badge-gold">
                                            <span class="status-dot dot-gold"></span> Draft
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right text-nowrap">
                                    <a href="<?php echo e(route('surat.show', $s)); ?>" class="action-btn btn-view" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('surat.edit', $s)); ?>" class="action-btn btn-edit" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="<?php echo e(route('surat.print', $s)); ?>" target="_blank" class="action-btn btn-view"
                                        title="Print">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="<?php echo e(route('surat.download', $s)); ?>" class="action-btn btn-download"
                                        title="Unduh .docx">
                                        <i class="fas fa-file-word"></i>
                                    </a>
                                    <form action="<?php echo e(route('surat.destroy', $s)); ?>" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus surat <?php echo e($s->nomor_surat); ?>?')">
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
                                    <i class="fas fa-envelope-open"></i>
                                    <div>Belum ada surat yang dibuat.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="pagination-info">Total <?php echo e($surats->total()); ?> surat</span>
                <?php echo e($surats->links('pagination::bootstrap-4')); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\AhsanDz\Claude\Projects\Surat_menyurat_Ngadri\resources\views/surat/index.blade.php ENDPATH**/ ?>