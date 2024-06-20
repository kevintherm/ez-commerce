<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="_token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>{{ $title ?? 'Dashboard' }} - EComm</title>
    <link href="/css/dashboard.style.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <script src="/js/jquery.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('head')
</head>

<body class="sb-nav-fixed">
    @include('dashboard.layouts.topnav')
    <div id="layoutSidenav">
        @include('dashboard.layouts.sidenav')
        <div id="layoutSidenav_content">
            <main>
                <h4 class="p-3" style="font-weight: 600">Welcome Back, {{ $user->name ?? 'User' }}</h4>
                @yield('main')
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; {{ Str::ucfirst(env('APP_NAME')) ?? 'Website' }}
                            {{ date('Y') }}
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="/js/bootstrap5.js"></script>
    <script src="/js/mobile-check.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', event => {

            // Toggle the side navigation
            const sidebarToggle = document.body.querySelector('#sidebarToggle');
            if (sidebarToggle) {
                // Uncomment Below to persist sidebar toggle between refreshes
                // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
                //     document.body.classList.toggle('sb-sidenav-toggled');
                // }
                sidebarToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                    localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains(
                        'sb-sidenav-toggled'));
                });
            }

        });
        if (mobileCheck()) {
            $('.modal-dialog').addClass('modal-fullscreen')
        }
    </script>
    @if (session('alert'))
        <script>
            swal.fire({
                text: "{{ session('alert') }}"
            })
        </script>
    @endif
    @if (session('msg'))
        <script>
            swal.fire({
                text: '{{ session('msg')['body'] ?? '' }}',
                icon: '{{ session('msg')['status'] ?? '' }}',
                title: '{{ session('msg')['title'] ?? '' }}'
            })
        </script>
    @endif
    @if (session('toast'))
        <script>
            swal.fire({
                text: '{{ session('toast')['text'] ?? '' }}',
                close: true
            })
        </script>
    @endif
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
    </script>
    @stack('foot')
</body>

</html>
