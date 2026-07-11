<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="theme-@yield('theme', 'matrix')">

    @if(trim($__env->yieldContent('theme')) === 'matrix')
        <canvas id="matrix"></canvas>
    @endif

    <div class="container">
        <h1>@yield('code')</h1>
        <p>@yield('message')</p>

        @yield('content')

        <a href="{{ url('/') }}" class="back-home">
            @yield('button_text', 'Return to Safe Zone')
        </a>
    </div>

    @if(trim($__env->yieldContent('theme')) === 'fish')
        <div class="fish">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="shadow"></div>
        <div class="bubbles">
            @for($i = 0; $i < 8; $i++)
                <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/88939/drop.png" class="bubble b{{ $i + 1 }}" />
            @endfor
        </div>
    @endif

    @yield('scripts')

    @if(trim($__env->yieldContent('theme')) === 'matrix')
        <script>
            const canvas = document.getElementById("matrix");
            if (canvas) {
                const ctx = canvas.getContext("2d");
                canvas.height = window.innerHeight;
                canvas.width = window.innerWidth;
                const letters = "01";
                const fontSize = 14;
                const columns = canvas.width / fontSize;
                const drops = [];
                for (let x = 0; x < columns; x++) drops[x] = 1;

                function draw() {
                    ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = "#00ff41";
                    ctx.font = fontSize + "px monospace";
                    for (let i = 0; i < drops.length; i++) {
                        const text = letters[Math.floor(Math.random() * letters.length)];
                        ctx.fillText(text, i * fontSize, drops[i] * fontSize);
                        if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) drops[i] = 0;
                        drops[i]++;
                    }
                }
                setInterval(draw, 33);
            }
        </script>
    @endif
</body>

</html>