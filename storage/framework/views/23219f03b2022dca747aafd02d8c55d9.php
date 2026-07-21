
<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Welcome Card Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <h2>Halo, <?php echo e(Auth::user()->name); ?>! 🌾</h2>
                <p>Selamat datang kembali di Panel Administrasi Desa Ngadri. Gunakan dashboard ini untuk membuat surat
                    penduduk, memantau riwayat pelayanan, dan mengelola master data dengan praktis.</p>
            </div>
        </div>
    </div>

    <!-- Stats Card Modern Grid -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-modern stat-primary">
                <div>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value"><?php echo e(number_format($stats['total_penduduk'], 0, ',', '.')); ?></div>
                    <div class="stat-label">Data Penduduk</div>
                </div>
                <a href="<?php echo e(route('penduduk.index')); ?>" class="stat-action">Lihat data <i
                        class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-modern stat-info">
                <div>
                    <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="stat-value"><?php echo e(number_format($stats['total_surat'], 0, ',', '.')); ?></div>
                    <div class="stat-label">Total Surat Dibuat</div>
                </div>
                <a href="<?php echo e(route('surat.index')); ?>" class="stat-action">Riwayat <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-modern stat-warning">
                <div>
                    <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                    <div class="stat-value"><?php echo e($stats['surat_hari_ini']); ?></div>
                    <div class="stat-label">Surat Hari Ini</div>
                </div>
                <a href="<?php echo e(route('surat.create')); ?>" class="stat-action">Buat surat <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-modern stat-danger">
                <div>
                    <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                    <div class="stat-value"><?php echo e($stats['total_jenis']); ?></div>
                    <div class="stat-label">Jenis Surat Aktif</div>
                </div>
                <a href="<?php echo e(route('jenis-surat.index')); ?>" class="stat-action">Kelola <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-8 mb-3">
            <div class="card-modern">
                <div class="card-header"><span class="card-title"><i class="fas fa-chart-line mr-2"></i> Statistik Surat per
                        Bulan</span></div>
                <div class="card-body"><canvas id="chartBulanan" height="110"></canvas></div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card-modern">
                <div class="card-header"><span class="card-title"><i class="fas fa-chart-pie mr-2"></i> Jenis Surat
                        Terbanyak</span></div>
                <div class="card-body"><canvas id="chartJenis" height="200"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Perangkat Desa (Penandatangan) -->
    <div class="card-modern mb-4">
        <div class="card-header"><span class="card-title"><i class="fas fa-user-tie mr-2"></i> Perangkat Desa
                Penandatangan</span></div>
        <div class="card-body">
            <p class="text-muted mb-3">Nama berikut otomatis dipakai pada bagian tanda tangan surat. Saat membuat surat,
                Anda dapat memilih siapa yang menandatangani (default Kepala Desa). Nama dapat diubah kapan saja.</p>
            <form action="<?php echo e(route('settings.pejabat')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label><i class="fas fa-user-shield mr-1"></i> Kepala Desa</label>
                        <input type="text" name="kepala_desa"
                            class="form-control <?php $__errorArgs = ['kepala_desa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('kepala_desa', $kepalaDesa)); ?>" required>
                        <?php $__errorArgs = ['kepala_desa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label><i class="fas fa-user-edit mr-1"></i> Sekretaris Desa</label>
                        <input type="text" name="sekretaris_desa"
                            class="form-control <?php $__errorArgs = ['sekretaris_desa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('sekretaris_desa', $sekretarisDesa)); ?>" required>
                        <?php $__errorArgs = ['sekretaris_desa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Nama Perangkat</button>
            </form>
        </div>
    </div>

    <!-- Logo Kop Surat -->
    <div class="card-modern mb-4">
        <div class="card-header"><span class="card-title"><i class="fas fa-image mr-2"></i> Logo Kop Surat</span></div>
        <div class="card-body">
            <p class="text-muted mb-3">Unggah logo untuk kop surat. Setelah disimpan, logo otomatis diterapkan ke
                <strong>seluruh template surat</strong> (kiri: logo kabupaten, kanan: logo desa). Surat yang dibuat
                setelahnya akan memakai logo baru.</p>
            <form action="<?php echo e(route('settings.logo')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label><i class="fas fa-landmark mr-1"></i> Logo Kabupaten (kiri)</label>
                        <div class="d-flex align-items-center">
                            <img id="preview-logo_kabupaten"
                                src="<?php echo e($logoKabupaten ? asset('storage/'.$logoKabupaten).'?v='.time() : ''); ?>"
                                alt="Logo Kabupaten"
                                style="height:52px;width:52px;object-fit:contain;border:1px solid #e2e8f0;border-radius:6px;padding:3px;margin-right:10px;background:#fff;<?php echo e($logoKabupaten ? '' : 'display:none;'); ?>">
                            <div class="flex-grow-1">
                                <input type="file" name="logo_kabupaten" accept=".png,.jpg,.jpeg" data-preview="preview-logo_kabupaten"
                                    class="form-control-file logo-input <?php $__errorArgs = ['logo_kabupaten'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <?php $__errorArgs = ['logo_kabupaten'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback d-block"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <small class="text-muted">PNG/JPG, maks. 2 MB.
                            <?php echo e($logoKabupaten ? '' : 'Belum diubah — masih memakai logo bawaan template.'); ?></small>
                    </div>
                    <div class="form-group col-md-6">
                        <label><i class="fas fa-home mr-1"></i> Logo Desa (kanan)</label>
                        <div class="d-flex align-items-center">
                            <img id="preview-logo_desa"
                                src="<?php echo e($logoDesa ? asset('storage/'.$logoDesa).'?v='.time() : ''); ?>"
                                alt="Logo Desa"
                                style="height:52px;width:52px;object-fit:contain;border:1px solid #e2e8f0;border-radius:6px;padding:3px;margin-right:10px;background:#fff;<?php echo e($logoDesa ? '' : 'display:none;'); ?>">
                            <div class="flex-grow-1">
                                <input type="file" name="logo_desa" accept=".png,.jpg,.jpeg" data-preview="preview-logo_desa"
                                    class="form-control-file logo-input <?php $__errorArgs = ['logo_desa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <?php $__errorArgs = ['logo_desa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback d-block"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <small class="text-muted">PNG/JPG, maks. 2 MB.
                            <?php echo e($logoDesa ? '' : 'Belum diatur — sel kanan kop akan diisi saat logo diunggah.'); ?></small>
                    </div>
                </div>
                <button class="btn btn-primary"><i class="fas fa-upload mr-1"></i> Simpan &amp; Terapkan ke Semua
                    Template</button>
            </form>
        </div>
    </div>

    <!-- Activity Row -->
    <div class="card-modern">
        <div class="card-header"><span class="card-title"><i class="fas fa-history mr-2"></i> Aktivitas Terbaru</span></div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Nomor Surat</th>
                        <th>Jenis</th>
                        <th>Penduduk</th>
                        <th>Tanggal</th>
                        <th>Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $aktivitas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><a href="<?php echo e(route('surat.show', $s)); ?>"><?php echo e($s->nomor_surat); ?></a></td>
                            <td><?php echo e($s->jenisSurat->nama_surat ?? '-'); ?></td>
                            <td><?php echo e($s->penduduk->nama_lengkap ?? '-'); ?></td>
                            <td><?php echo e($s->tanggal_surat?->format('d/m/Y')); ?></td>
                            <td><?php echo e($s->user->name ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas pembuatan surat.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        const brand = '#1C6B47'; // Medium Green
        new Chart(document.getElementById('chartBulanan'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartBulanan['labels'], 15, 512) ?>,
                datasets: [{
                    label: 'Jumlah Surat Dibuat',
                    data: <?php echo json_encode($chartBulanan['data'], 15, 512) ?>,
                    borderColor: brand,
                    backgroundColor: 'rgba(28, 107, 71, 0.08)',
                    fill: true,
                    tension: .38,
                    pointRadius: 4,
                    pointBackgroundColor: brand,
                    borderWidth: 2.5
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.03)' },
                        ticks: { precision: 0 }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        new Chart(document.getElementById('chartJenis'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($chartJenis['labels'], 15, 512) ?>,
                datasets: [{
                    data: <?php echo json_encode($chartJenis['data'], 15, 512) ?>,
                    backgroundColor: ['#1C6B47', '#2C7FA8', '#E3A93C', '#C8492C', '#0E2A22'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { family: 'Plus Jakarta Sans', weight: '500' }
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // Pratinjau logo sebelum diunggah
        document.querySelectorAll('.logo-input').forEach(function (input) {
            input.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file || !file.type.startsWith('image/')) return;
                const img = document.getElementById(this.dataset.preview);
                const reader = new FileReader();
                reader.onload = e => {
                    img.src = e.target.result;
                    img.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\AhsanDz\Claude\Projects\Surat_menyurat_Ngadri\resources\views/dashboard/index.blade.php ENDPATH**/ ?>