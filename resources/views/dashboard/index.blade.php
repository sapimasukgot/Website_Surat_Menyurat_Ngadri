@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <!-- Welcome Card Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <h2>Halo, {{ Auth::user()->name }}! 🌾</h2>
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
                    <div class="stat-value">{{ number_format($stats['total_penduduk'], 0, ',', '.') }}</div>
                    <div class="stat-label">Data Penduduk</div>
                </div>
                <a href="{{ route('penduduk.index') }}" class="stat-action">Lihat data <i
                        class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-modern stat-info">
                <div>
                    <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="stat-value">{{ number_format($stats['total_surat'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total Surat Dibuat</div>
                </div>
                <a href="{{ route('surat.index') }}" class="stat-action">Riwayat <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-modern stat-warning">
                <div>
                    <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                    <div class="stat-value">{{ $stats['surat_hari_ini'] }}</div>
                    <div class="stat-label">Surat Hari Ini</div>
                </div>
                <a href="{{ route('surat.create') }}" class="stat-action">Buat surat <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-modern stat-danger">
                <div>
                    <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                    <div class="stat-value">{{ $stats['total_jenis'] }}</div>
                    <div class="stat-label">Jenis Surat Aktif</div>
                </div>
                <a href="{{ route('jenis-surat.index') }}" class="stat-action">Kelola <i class="fas fa-arrow-right"></i></a>
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
            <form action="{{ route('settings.pejabat') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label><i class="fas fa-user-shield mr-1"></i> Kepala Desa</label>
                        <input type="text" name="kepala_desa"
                            class="form-control @error('kepala_desa') is-invalid @enderror"
                            value="{{ old('kepala_desa', $kepalaDesa) }}" required>
                        @error('kepala_desa')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label><i class="fas fa-user-edit mr-1"></i> Sekretaris Desa</label>
                        <input type="text" name="sekretaris_desa"
                            class="form-control @error('sekretaris_desa') is-invalid @enderror"
                            value="{{ old('sekretaris_desa', $sekretarisDesa) }}" required>
                        @error('sekretaris_desa')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Nama Perangkat</button>
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
                    @forelse ($aktivitas as $s)
                        <tr>
                            <td><a href="{{ route('surat.show', $s) }}">{{ $s->nomor_surat }}</a></td>
                            <td>{{ $s->jenisSurat->nama_surat ?? '-' }}</td>
                            <td>{{ $s->penduduk->nama_lengkap ?? '-' }}</td>
                            <td>{{ $s->tanggal_surat?->format('d/m/Y') }}</td>
                            <td>{{ $s->user->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas pembuatan surat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const brand = '#1C6B47'; // Medium Green
        new Chart(document.getElementById('chartBulanan'), {
            type: 'line',
            data: {
                labels: @json($chartBulanan['labels']),
                datasets: [{
                    label: 'Jumlah Surat Dibuat',
                    data: @json($chartBulanan['data']),
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
                labels: @json($chartJenis['labels']),
                datasets: [{
                    data: @json($chartJenis['data']),
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
    </script>
@endpush