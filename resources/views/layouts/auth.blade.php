<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MasBoy POS')</title>
    <link rel="icon" type="image/png" href="{{ asset('Logo_MasBoy.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireStyles
    <style>
        .swal2-popup { font-family: 'Poppins', sans-serif !important; border-radius: 16px !important; }
        .swal2-timer-progress-bar { background: #064E3B !important; }
        .swal2-icon-success { border-color: #064E3B !important; color: #064E3B !important; }
        .swal2-icon-error { border-color: #DC2626 !important; color: #DC2626 !important; }
        .swal2-icon-warning { border-color: #D97706 !important; color: #D97706 !important; }
        .swal2-toast { background: #fff !important; padding: 12px 20px 12px 16px !important; border-radius: 12px !important; border: 1px solid #E5E7EB !important; box-shadow: 0 4px 16px rgba(0,0,0,0.08) !important; }
        .swal2-toast .swal2-title { font-size: 0.875rem !important; font-weight: 600 !important; color: #1F2937 !important; }
        .swal2-toast .swal2-timer-progress-bar { height: 3px !important; border-radius: 0 0 12px 12px !important; }
        .swal2-toast:has(.swal2-icon-success) { border-left: 4px solid #064E3B !important; }
        .swal2-toast:has(.swal2-icon-error) { border-left: 4px solid #DC2626 !important; }
        .swal2-toast:has(.swal2-icon-warning) { border-left: 4px solid #D97706 !important; }
        .swal2-confirm { border-radius: 12px !important; font-weight: 600 !important; font-size: 0.875rem !important; padding: 10px 28px !important; border: none !important; }
        .swal2-cancel { border-radius: 12px !important; font-weight: 600 !important; font-size: 0.875rem !important; padding: 10px 28px !important; border: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-900 to-emerald-900 min-h-screen flex items-center justify-center">
    @yield('content')
    <script>
        function toast(icon, title) {
            Swal.fire({
                icon: icon,
                title: title,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                iconColor: icon === 'success' ? '#064E3B' : (icon === 'error' ? '#DC2626' : '#D97706'),
                background: '#ffffff',
            });
        }
    </script>
    @if (session('success'))
        <script>toast('success', @json(session('success')));</script>
    @endif
    @if (session('error'))
        <script>toast('error', @json(session('error')));</script>
    @endif
    @livewireScripts
</body>
</html>
