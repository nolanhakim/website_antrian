<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'antrianQ')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-canvas font-sans text-ink selection:bg-accent-soft selection:text-accent antialiased">
    @if (session('status'))
        <script>
            (function() {
                const msg = @json(session('status'));
                const isInfo = /^(tidak ada|belum ada)/i.test(msg);
                const fire = () => {
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: isInfo ? 'info' : 'success',
                            title: isInfo ? 'Informasi' : 'Berhasil',
                            text: msg,
                            confirmButtonColor: '#0F766E',
                            confirmButtonText: 'OK',
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    } else {
                        setTimeout(fire, 30);
                    }
                };
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', fire);
                } else {
                    fire();
                }
            })();
        </script>
    @endif

    @if (session('error'))
        <script>
            (function() {
                const fire = () => {
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'error',
                            title: 'Perhatian',
                            text: @json(session('error')),
                            confirmButtonColor: '#0F766E',
                            confirmButtonText: 'OK',
                        });
                    } else {
                        setTimeout(fire, 30);
                    }
                };
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', fire);
                } else {
                    fire();
                }
            })();
        </script>
    @endif

    @yield('content')

    <script>
        // Mencegah double-submit: amankan nilai submitter lalu beri micro-feedback
        document.addEventListener('submit', function(e) {
            const btn = e.submitter || e.target.querySelector('button[type="submit"]');
            if (btn && !btn.dataset.noLoading) {
                if (btn.name && btn.value && !e.target.querySelector(`input[type="hidden"][name="${btn.name}"]`)) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = btn.name;
                    hidden.value = btn.value;
                    e.target.appendChild(hidden);
                }
                setTimeout(() => {
                    btn.disabled = true;
                    btn.style.opacity = '0.65';
                    btn.style.cursor = 'not-allowed';
                }, 0);
            }
        });
    </script>
</body>
</html>
