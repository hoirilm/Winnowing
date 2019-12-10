<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    {{-- jQuery --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ url('template/src/assets/vendors/iconfonts/mdi/css/materialdesignicons.css') }}">
    <!-- endinject -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ url('template/src/assets/css/shared/style.css') }}">
    <!-- endinject -->
    <!-- Layout style -->
    <link rel="stylesheet" href="{{ url('template/src/assets/css/demo_1/style.css') }}">
    <!-- Layout style -->
    <link rel="shortcut icon" href="{{ url('template/src/asssets/images/favicon.ico') }}" />
    {{-- Apex Chart --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    





</head>

<body class="header-fixed">
    <!-- partial:partials/_header.html -->
    <nav class="t-header">
        <div class="t-header-brand-wrapper">
            <a href="/">
                <img class="logo logo w-75 mt-5 ml-3" src="{{ url('template/src/assets/images/logo.png') }}" alt="">
                <img class="logo-mini" src="{{ url('template/src/assets/images/mini.png') }}" alt="">
            </a>
        </div>
        <div class="t-header-content-wrapper">
            <div class="t-header-content">
                <button class="t-header-toggler t-header-mobile-toggler d-block d-lg-none">
                    <i class="mdi mdi-menu"></i>
                </button>
            </div>
        </div>
    </nav>
    <!-- partial -->
    <div class="page-body">
        <!-- partial:partials/_sidebar.html -->
        <div class="sidebar">
            <ul class="navigation-menu">
                <li class="nav-category-divider">MAIN MENU</li>
                <li>
                    <a href="{{ url('/') }}">
                        <span class="link-title">Home</span>
                        <i class="mdi mdi-home link-icon"></i>
                    </a>
                </li>
                <li>
                    <a href={{ url('corpus') }}>
                        <span class="link-title">Corpus</span>
                        <i class="mdi mdi-database link-icon"></i>
                    </a>
                </li>
                <li>
                    <a href={{ url('advance') }}>
                        <span class="link-title">Advanced</span>
                        <i class="mdi mdi-settings link-icon"></i>
                    </a>
                </li>
                <li>
                    <a href="#bidang" data-toggle="collapse" aria-expanded="false">
                        <span class="link-title">Bulk Search</span>
                        <i class="mdi mdi-file-multiple link-icon"></i>
                    </a>
                    <ul class="collapse navigation-submenu" id="bidang">
                        <li>
                            <a href={{ url('onetomany') }}>One to Many</a>
                        </li>
                        <li>
                            <a href={{ url('manytomany') }}>Many to Many</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href={{ url('translate') }}>
                        <span class="link-title">Translate Document</span>
                        <i class="mdi mdi mdi-google-translate link-icon"></i>
                    </a>
                </li>
            </ul>
        </div>
        <!-- partial -->
        <div class="page-content-wrapper">
            <div class="page-content-wrapper-inner">
                <div class="content-viewport">
                    <div class="row">
                        @yield('container')
                    </div>
                </div>
            </div>
            <!-- content viewport ends -->
            <!-- partial:partials/_footer.html -->
            <footer class="footer p-3">
                <div class="row">
                    <div class="col-sm-6 text-sm-left mt-3 mt-sm-0">
                        <small class="text-gray mt-2">Handcrafted With <i class="mdi mdi-heart text-danger"></i></small>
                    </div>
                </div>
            </footer>
            <!-- partial -->
        </div>
        <!-- page content ends -->
    </div>
    <!--page body ends -->
    <!-- SCRIPT LOADING START FORM HERE /////////////-->

    <!-- plugins:js -->
    <script src="{{ url('template/src/assets/vendors/js/core.js') }}"></script>
    <!-- endinject -->
    <!-- jQuery -->
    <script src="//code.jquery.com/jquery.js"></script>
    <!-- DataTables -->
    <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <!-- App scripts -->
    {{-- @stack('scripts') --}}


    <!-- Vendor Js For This Page Ends-->
    <script src="{{ url('template/src/assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ url('template/src/assets/vendors/chartjs/Chart.min.js') }}"></script>
    <script src="{{ url('template/src/assets/js/charts/chartjs.addon.js') }}"></script>
    <!-- Vendor Js For This Page Ends-->
    <!-- build:js -->
    <script src="{{ url('template/src/assets/js/template.js') }}"></script>
    <script src="{{ url('template/src/assets/js/dashboard.js') }}"></script>
    {{-- Smooth Scrolling --}}
</body>

</html>