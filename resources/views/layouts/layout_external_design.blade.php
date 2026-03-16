<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Diseño por invitación') | PARTILOT</title>
    <link rel="shortcut icon" href="{{ url('/') }}/logo.svg">
    <link href="{{ url('default') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('default') }}/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{ url('style.css') }}">
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/45.2.0/ckeditor5.css" crossorigin>
    @yield('styles')
</head>
<body class="auth-body">
    <div class="navbar navbar-expand-lg border-bottom bg-white">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="{{ url('/') }}/logo.svg" alt="Partilot" height="32">
                <span class="text-dark fw-semibold">Partilot</span>
                <span class="badge bg-warning text-dark ms-2">Diseño por invitación</span>
            </a>
        </div>
    </div>
    <div id="wrapper" class="mt-0">
        <div class="content-page">
            <div class="content">
                <div class="container-fluid p-3">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <script src="{{ url('default') }}/assets/js/vendor.min.js"></script>
    {{-- app.min.js no se carga aquí: espera DOM del layout completo (sidebar/topbar) y daría "reading 'href' of null" --}}
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script src="{{ url('ckeditor/ckeditor.js') }}"></script>
    <script src="{{ url('ckeditor/adapters/jquery.js') }}"></script>
    @yield('scripts')
</body>
</html>
