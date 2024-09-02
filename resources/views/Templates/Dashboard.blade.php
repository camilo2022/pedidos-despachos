<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ Auth::user()->id }}">
    <meta name="business-id" content="{{ Auth::user()->business_id }}">
    <meta name="title" content="{{ Auth::user()->title }}">
    <title>BLESS</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/plugins/fontawesome-free/css/all.min.css') }}">

    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/sharp-solid.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/sharp-regular.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/sharp-light.css">
        <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('css/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('css/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('css/plugins/toastr/toastr.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('css/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('css/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('css/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('css/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- Chosen Choise -->
    <link rel="stylesheet" href="{{ asset('css/plugins/chosen-choise/main_choices.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/chosen-choise/chosen.min.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('css/plugins/table/table.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- fullCalendar -->
    <link rel="stylesheet" href="{{ asset('css/plugins/fullcalendar/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/fullcalendar-daygrid/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/fullcalendar-timegrid/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/fullcalendar-bootstrap/main.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/plugins/ekko-lightbox/ekko-lightbox.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('css/plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('css/plugins/select2/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/select2-bootstrap4-theme/select2-bootstrap4.css') }}">
    <!-- Dropify -->
    <link rel="stylesheet" href="{{ asset('css/plugins/dropify/dropify.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/dropify/dropify.min.css') }}">
    <!-- Dropzone -->
    <link rel="stylesheet" href="{{ asset('css/plugins/dropzone/dropzone.min.css') }}">
    <style>
        .c_form_group {
            border: 1px solid #e9ecef;
            text-align: left;
            padding: 10px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark navbar-gray-dark">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button" id="SideBarButton">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/Dashboard" class="nav-link">Home</a>
                </li>
                <!-- <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link">Contact</a>
                </li> -->
            </ul>

            <!-- SEARCH FORM -->
            <form class="form-inline ml-3">
                <div class="input-group input-group-sm">
                    <input class="form-control form-control-navbar" type="search" placeholder="Search"
                        aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-navbar" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-comments"></i>
                        <span class="badge badge-danger navbar-badge">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a href="#" class="dropdown-item">

                            <div class="media">
                                <img src="{{ asset('css/dist/img/user1-128x128.jpg') }}" alt="User Avatar"
                                    class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Brad Diesel
                                        <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">Call me whenever you can...</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>

                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">

                            <div class="media">
                                <img src="{{ asset('css/dist/img/user8-128x128.jpg') }}" alt="User Avatar"
                                    class="img-size-50 img-circle mr-3">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        John Pierce
                                        <span class="float-right text-sm text-muted"><i
                                                class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">I got your message bro</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>

                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">

                            <div class="media">
                                <img src="{{ asset('css/dist/img/user3-128x128.jpg') }}" alt="User Avatar"
                                    class="img-size-50 img-circle mr-3">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Nora Silvester
                                        <span class="float-right text-sm text-warning"><i
                                                class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">The subject goes here</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>

                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
                    </div>
                </li> -->
                <!-- Notifications Dropdown Menu -->
                <!-- <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge">15</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">15 Notifications</span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> 4 new messages
                            <span class="float-right text-muted text-sm">3 mins</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-users mr-2"></i> 8 friend requests
                            <span class="float-right text-muted text-sm">12 hours</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file mr-2"></i> 3 new reports
                            <span class="float-right text-muted text-sm">2 days</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                    </div>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link" style="cursor: pointer;" onclick="document.logout.submit();">
                        <i class="fas fa-power-off"></i>
                    </a>
                    <form name="logout" action="{{ route('logout') }}" method="POST">
                        @csrf
                    </form>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"
                        role="button">
                        <i class="fas fa-th-large"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar elevation-4 sidebar-dark-lightblue">
            <!-- Brand Logo -->
            <a href="/Dashboard" class="brand-link">
                <img src="{{ asset('css/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light"><b>ORG. BLESS</b></span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="{{ asset('css/dist/img/avatar.png') }}" class="img-circle elevation-2"
                                onerror="this.src = `{{ asset('css/dist/img/user2-160x160.jpg') }}`" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ Auth::user()->name . ' ' . Auth::user()->last_name }}</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-child-indent" data-widget="treeview" role="menu"
                        data-accordion="false">

                        <li class="nav-item">
                            <a href="/Dashboard" class="nav-link {{ Request::route()->getName() === 'Dashboard' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>

                        @foreach ($items as $item)
                            <li class="nav-item has-treeview {{  in_array(Request::route()->getName(), $item->submodules->pluck('permission')->toArray())  ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{  in_array(Request::route()->getName(), $item->submodules->pluck('permission')->toArray())  ? 'active' : '' }}">
                                    <i class="{{ 'nav-icon '.$item->icon }}"></i>
                                    <p>
                                        {{ $item->name }}
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @foreach ($item->submodules as $subitem)
                                    <li class="nav-item">
                                        <a href="{{ $subitem->url }}" class="nav-link {{ Request::route()->getName() === $subitem->permission ? 'active' : '' }}">
                                            <i class="{{ 'nav-icon '.$subitem->icon }}"></i>
                                            <p>{{ $subitem->name }}</p>
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach

                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <!-- /.content-header -->

            <!-- Main content -->
            @yield('content')
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <strong>Copyright &copy; {{ Carbon::now()->format('Y') }} <a href="https://orgbless.com" target="_blank">ORGANIZACIÓN BLESS S.A.S</a>.</strong>
            Todos los derechos reservados.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('js/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('js/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('js/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('js/sparklines/sparkline.js') }}"></script>
    <!-- JQVMap -->
    <script src="{{ asset('js/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('js/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
        <script src="{{ asset('js/jqvmap/maps/jquery.vmap.world.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('js/moment/moment.min.js') }}"></script>
    <script src="{{ asset('js/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('js/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('js/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('js/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('js/dist/js/adminlte.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <!-- DataTables -->
    <script src="{{ asset('js/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('js/dist/js/demo.js') }}"></script>

    <script src="{{ asset('js/dist/js/demo.js') }}"></script>
    <!-- jQuery Mapael -->
    <script src="{{ asset('js/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset('js/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('js/jquery-mapael/jquery.mapael.min.js') }}"></script>
    <script src="{{ asset('js/jquery-mapael/maps/usa_states.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('js/chart.js/Chart.min.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('js/jquery-knob/jquery.knob.min.js') }}"></script>
    <!-- FLOT CHARTS -->
    <script src="{{ asset('js/flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('js/flot-old/jquery.flot.resize.min.js') }}"></script>
    <script src="{{ asset('js/flot-old/jquery.flot.pie.min.js') }}"></script>
    <!-- fullCalendar 2.2.5 -->
    <script src="{{ asset('js/moment/moment.min.js') }}"></script>
    <script src="{{ asset('js/fullcalendar/main.min.js') }}"></script>
    <script src="{{ asset('js/fullcalendar-daygrid/main.min.js') }}"></script>
    <script src="{{ asset('js/fullcalendar-timegrid/main.min.js') }}"></script>
    <script src="{{ asset('js/fullcalendar-interaction/main.min.js') }}"></script>
    <script src="{{ asset('js/fullcalendar-bootstrap/main.min.js') }}"></script>
    <!-- jquery-validation -->
    <script src="{{ asset('js/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/jquery-validation/additional-methods.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('js/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <!-- Chosen Choise -->
    <script src="{{ asset('js/chosen-choise/chosen.jquery.min.js') }}"></script>
    <script src="{{ asset('js/chosen-choise/choices.min.js') }}"></script>

    <script src="{{ asset('js/ekko-lightbox/ekko-lightbox.min.js') }}"></script>
    <script src="{{ asset('js/filterizr/jquery.filterizr.min.js') }}"></script>

    <script src="{{ asset('js/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/datatables-buttons/js/dataTables.buttons.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

    <script src="{{ asset('js/select2/js/select2.full.min.js') }}"></script>

    <!-- Dropify -->
    <script src="{{ asset('js/dropify/dropify.js') }}"></script>
    <script src="{{ asset('js/dropify/dropify.min.js') }}"></script>

    <!-- Dropify -->
    <script src="{{ asset('js/dropzone/dropzone.min.js') }}"></script>

    <script src="{{ asset('js/dist/js/Validators.js') }}"></script>


    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        $('.select2').select2({
            tags: "true",
            allowClear: true
        });
        
        $('.dropify').dropify();


        $("input[data-bootstrap-switch]").each(function(){
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        })
    </script>
    @yield('script')

</body>

</html>
