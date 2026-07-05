@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner"><h3>{{ number_format($stats['total_penduduk'], 0, ',', '.') }}</h3><p>Data Penduduk</p></div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ route('penduduk.index') }}" class="small-box-footer">Lihat data <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner"><h3>{{ number_format($stats['total_surat'], 0, ',', '.') }}</h3><p>Total Surat Dibuat</p></div>
            <div class="icon"><i class="fas fa-file-alt"></i></div>
            <a href="{{ route('surat.index') }}" class="small-box-footer">Riwayat <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner"><h3>{{ $stats['surat_hari_ini'] }}</h3><p>Surat Hari Ini</p></div>
            <div class="icon"><i class="fas fa-calendar-day"></i></div>
            <a href="{{ route('surat.create') }}" class="small-box-footer">Buat surat <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner"><h3>{{ $stats['total_jenis'] }}</h3><p>Jenis Surat Aktif</p></div>
            <div class="icon"><i class="fas fa-layer-group"></i></div>
            <a href="{{ route('jenis-surat.index') }}" class="small-box-footer">Kelola <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Statistik Surat per Bulan</h3></div>
            <div class="card-body"><canvas id="chartBulanan" height="110"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i> Jenis Surat Terbanyak</h3></div>
            <div class="card-body"><canvas id="chartJenis" height="200"></canvas></div>
        </div>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header"><h3 class="card-title"><i class="fas fa-history mr-1"></i> Aktivitas Terbaru</h3></div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead><tr><th>Nomor Surat</th><th>Jenis</th><th>Penduduk</th><th>Tanggal</th><th>Oleh</th></tr></thead>
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
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada surat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const brand = '#1d4ed8';
    new Chart(document.getElementById('chartBulanan'), {
        type: 'line',
        data: {
            labels: @json($chartBulanan['labels']),
            datasets: [{
                label: 'Jumlah Surat',
                data: @json($chartBulanan['data']),
                borderColor: brand, backgroundColor: 'rgba(29,78,216,.12)',
                fill: true, tension: .35, pointRadius: 3,
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });

    new Chart(document.getElementById('chartJenis'), {
        type: 'doughnut',
        data: {
            labels: @json($chartJenis['labels']),
            datasets: [{
                data: @json($chartJenis['data']),
                backgroundColor: ['#1d4ed8', '#0ea5e9', '#22c55e', '#f59e0b', '#94a3b8'],
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endpush
