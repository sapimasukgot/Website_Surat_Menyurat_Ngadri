<nav class="main-header navbar navbar-expand navbar-primary navbar-dark">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link">Beranda</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#">
                @if (Auth::user()->photo_url)
                    <img src="{{ Auth::user()->photo_url }}?v={{ Auth::user()->updated_at?->timestamp }}" alt="Foto"
                        style="width:26px;height:26px;object-fit:cover;border-radius:50%;margin-right:6px;border:2px solid rgba(255,255,255,0.6);">
                @else
                    <i class="far fa-user" style="margin-right:6px;"></i>
                @endif
                {{ Auth::user()->name }} <i class="fas fa-caret-down ml-1"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <span class="dropdown-item-text text-muted small">{{ Auth::user()->jabatan ?? 'Admin' }}</span>
                <div class="dropdown-divider"></div>
                <a href="{{ route('profile.edit') }}" class="dropdown-item"><i class="fas fa-id-card mr-2"></i> Profil</a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt mr-2"></i> Keluar</button>
                </form>
            </div>
        </li>
    </ul>
</nav>
