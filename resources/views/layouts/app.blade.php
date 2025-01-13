<!DOCTYPE html>
<html lang="en">

<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>
        @if (trim($__env->yieldContent('title')))
            @yield('title') | {{ config('app.name', 'sablonku') }}
        @else
            {{ config('app.name', 'sablonku') }}
        @endif
    </title>
    <meta name="theme-color" content="#ffffff">
    @stack('before-styles')
    <link rel="stylesheet" href="{{ asset('build/assets/app-aff29bc1.css') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/magnific-popup.css') }}">
    {{-- @vite('resources/sass/app.scss') --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    @stack('after-styles')
</head>

<body>
    <div class="sidebar sidebar-dark sidebar-fixed" id="sidebar">
        <div class="sidebar-brand d-none d-md-flex">
            @if (session()->has('logo'))
                <img style="height:50px"
                    src="{{ url('uploads/logo/' . session('logo')) }}"
                    alt="" srcset="">
            @endif
        </div>
        @include('layouts.navigation')
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
    <div class="wrapper d-flex flex-column min-vh-100 bg-light">
        <!-- Header block -->
        @include('layouts.includes.header')
        <!-- / Header block -->

        <div class="body flex-grow-1 px-3">
            <div class="container-fluid">
                <!-- Errors block -->
                @include('layouts.includes.errors')
                <!-- / Errors block -->
                <div class="mb-4">@yield('content')</div>
            </div>
        </div>

        <!-- Footer block -->
        @include('layouts.includes.footer')
        <!-- / Footer block -->
    </div>

    <div class="modal fade" id="modal-hapus" tabindex="-1" role="dialog" aria-labelledby="modal-notification"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Yakin ingin menghapus data ini?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <h4>pilih "hapus" jika anda yakin</h4>
                </div>
                <div class="modal-footer">
                    <form action="" method="post">
                        {{ csrf_field() }}
                        {{ method_field('delete') }}
                        <p>
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger waves-effect">Hapus</button>
                        </p>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <!-- Scripts -->
    @stack('before-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/coreui.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery.PrintArea.js') }}"></script>
    <script src="{{ asset('js/jquery.magnific-popup.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
    </script>
    {{-- @vite('resources/js/app.js') --}}
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
    @stack('after-scripts')
    <!-- / Scripts -->

</body>

</html>
