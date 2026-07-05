@php($isPenduduk = request()->routeIs('penduduk.*'))
@php($isJenis = request()->routeIs('jenis-surat.*'))
@php($isSurat = request()->routeIs('surat.*'))
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link text-center">
        <i class="fas fa-envelope-open-text ml-2"></i>
        <span class="brand-text font-weight-light">Surat Desa</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image"><i class="fas fa-user-circle fa-2x text-white-50"></i></div>
            <div class="info">
                <span class="d-block text-white">{{ Auth::user()->name }}</span>
                <small class="text-white-50">{{ Auth::user()->jabatan ?? 'Administrator' }}</small>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-header text-uppercase">Master Data</li>
                <li class="nav-item">
                    <a href="{{ route('penduduk.index') }}" class="nav-link {{ $isPenduduk ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Data Penduduk</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('jenis-surat.index') }}" class="nav-link {{ $isJenis ? 'active' : '' }}">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>Jenis Surat</p>
                    </a>
                </li>
                <li class="nav-header text-uppercase">Pelayanan</li>
                <li class="nav-item">
                    <a href="{{ route('surat.create') }}" class="nav-link {{ request()->routeIs('surat.create') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-signature"></i>
                        <p>Buat Surat</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('surat.index') }}" class="nav-link {{ request()->routeIs('surat.index') || request()->routeIs('surat.show') || request()->routeIs('surat.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Riwayat Surat</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
