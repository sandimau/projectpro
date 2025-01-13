<!DOCTYPE html>
<html lang="en">

<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>{{ config('app.name', 'sablonku') }}</title>
    <meta name="theme-color" content="#ffffff">
    @stack('before-styles')
    <link rel="stylesheet" href="{{ asset('build/assets/app-aff29bc1.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    @stack('after-styles')
</head>

<body>

    <div class="bg-light min-vh-100 d-flex flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                @yield('content')
            </div>
        </div>
    </div>
    @stack('before-scripts')
    <script src="{{ asset('build/assets/app-5f772514.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/coreui.bundle.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
    @stack('after-scripts')
</body>

</html>
