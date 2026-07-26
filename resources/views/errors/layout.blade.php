<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>

<body class="hold-transition" style="background:var(--brand-bg);">

    <div class="d-flex align-items-center justify-content-center" style="min-height:100vh;padding:1.5rem;">
        <div class="text-center" style="max-width:520px;">
            <div style="font-size:5.5rem;line-height:1;font-weight:800;color:var(--brand-primary);letter-spacing:-3px;">
                @yield('code')
            </div>
            <h1 style="font-size:1.35rem;font-weight:700;color:var(--brand-primary-dark);margin:1rem 0 .5rem;">
                @yield('heading', 'Terjadi Kesalahan')
            </h1>
            <p class="text-muted" style="font-size:0.98rem;">@yield('message')</p>

            @yield('content')

            <a href="{{ url('/') }}" class="btn btn-primary mt-3">
                <i class="fas fa-home mr-1"></i> @yield('button_text', 'Kembali ke Beranda')
            </a>
        </div>
    </div>

    @yield('scripts')
</body>

</html>
