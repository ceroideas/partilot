<!DOCTYPE html>
<html lang="es" data-topbar-color="light" data-layout-mode="detached" data-bs-theme="light">

    
<!-- Mirrored from coderthemes.com/ubold/layouts/default/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 25 May 2025 15:56:35 GMT -->
<head>
        <meta charset="utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title') | PARTILOT</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />

        <link href="{{url('assets')}}/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <link href="{{url('assets')}}/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <link href="{{url('assets')}}/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <link href="{{url('assets')}}/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css" rel="stylesheet" type="text/css" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{url('/')}}/logo.svg">

        <!-- Plugins css -->
        <link href="{{url('assets')}}/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
        <link href="{{url('assets')}}/libs/selectize/css/selectize.bootstrap3.css" rel="stylesheet" type="text/css" />
        
        <!-- Theme Config Js -->
        <script src="{{url('default')}}/assets/js/head.js"></script>

        <!-- Bootstrap css -->
        <link href="{{url('default')}}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="app-style" />

        <!-- App css -->
        <link href="{{url('default')}}/assets/css/app.min.css" rel="stylesheet" type="text/css" />

        <!-- Icons css -->
        <link href="{{url('assets')}}/css/icons.min.css" rel="stylesheet" type="text/css" />

        <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">

        <link rel="stylesheet" href="{{url('style.css')}}">
        <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/45.2.0/ckeditor5.css" crossorigin>

        <style>
            .inline-fields {

                width: 100%;
                min-height: 36px !important;
                background-color: transparent;
                border: none;
                border-bottom: 1px solid silver;
                outline: none;
              }
        </style>

    </head>

    <body>

        <!-- Begin page -->
        <div id="wrapper">

            
            <!-- ========== Menu ========== -->
            <div class="app-menu">  

                <!-- Brand Logo -->
                <div class="logo-box">
                    <!-- Brand Logo Light -->
                    <a href="index.html" class="logo-light">
                        {{-- <img src="{{url('default')}}/assets/images/logo-light.png" alt="logo" class="logo-lg"> --}}
                        <img src="{{url('/')}}/logo_menu.svg" alt="logo" class="logo-lg">
                        {{-- <img src="{{url('default')}}/assets/images/logo-sm.png" alt="small logo" class="logo-sm"> --}}
                        <img src="{{url('/')}}/logo.svg" alt="small logo" class="logo-sm">
                    </a>

                    <!-- Brand Logo Dark -->
                    <a href="index.html" class="logo-dark">
                        {{-- <img src="{{url('default')}}/assets/images/logo-dark.png" alt="dark logo" class="logo-lg"> --}}
                        <img src="{{url('/')}}/logo_menu.svg" alt="dark logo" class="logo-lg">
                        {{-- <img src="{{url('default')}}/assets/images/logo-sm.png" alt="small logo" class="logo-sm"> --}}
                        <img src="{{url('/')}}/logo.svg" alt="small logo" class="logo-sm">
                    </a>
                </div>

                <!-- menu-left -->
                <div class="scrollbar">

                    <!-- User box -->
                    <div class="user-box text-center">
                        <img src="{{url('default')}}/assets/images/users/user-1.jpg" alt="user-img" title="{{ Auth::user()->name ?? 'Usuario' }}" class="rounded-circle avatar-md">
                        <div class="dropdown">
                            <a href="javascript: void(0);" class="dropdown-toggle h5 mb-1 d-block" data-bs-toggle="dropdown">{{ Auth::user()->name ?? 'Usuario' }}</a>
                            <div class="dropdown-menu user-pro-dropdown">

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="fe-user me-1"></i>
                                    <span>Mi Cuenta</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="fe-settings me-1"></i>
                                    <span>Configuración</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="fe-lock me-1"></i>
                                    <span>Bloquear Pantalla</span>
                                </a>

                                <!-- item-->
                                <form method="POST" action="{{ url('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item notify-item" style="background: none; border: none; width: 100%; text-align: left;">
                                        <i class="fe-log-out me-1"></i>
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </form>

                            </div>
                        </div>
                        <p class="text-muted mb-0">{{ Auth::user()->email ?? 'admin@partilot.com' }}</p>
                    </div>

                    <!--- Menu -->
                    <ul class="menu">
                        @php $selected = null; @endphp

                        <li class="menu-title">Navigation</li>

                        <li class="menu-item @if (Request::is('dashboard') || Request::is('/')) menuitem-active @php $selected = 1; @endphp @endif">
                            <a href="{{url('/dashboard')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img src="{{url('icons')}}/dashboard{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Panel </span>
                                @php $selected = null; @endphp
                            </a>
                        </li>

                        <li class="menu-item @if (Request::is('administrations/*') || Request::is('administrations')) menuitem-active @php $selected = 1; @endphp @endif">
                            <a href="{{url('/administrations')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img src="{{url('icons')}}/administraciones{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Administraciones </span>
                                @php $selected = null; @endphp
                            </a>
                        </li>

                        <li class="menu-item @if (Request::is('entities/*') || Request::is('entities')) menuitem-active @php $selected = 1; @endphp @endif">
                            <a href="{{url('/entities')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img src="{{url('icons')}}/entidades{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Entidades </span>
                                @php $selected = null; @endphp
                            </a>
                        </li>

                        <li class="menu-item @if (Request::is('sellers/*') || Request::is('sellers')) menuitem-active @php $selected = 1; @endphp @endif">
                            <a href="{{url('/sellers')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img src="{{url('icons')}}/vendedores{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Vendedores/Asignación </span>
                                @php $selected = null; @endphp
                            </a>
                        </li>

                        <li class="menu-item @if (Request::is('users/*') || Request::is('users')) menuitem-active @php $selected = 1; @endphp @endif">
                            <a href="{{url('/users')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img src="{{url('icons')}}/usuarios{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Usuarios </span>
                                @php $selected = null; @endphp
                            </a>
                        </li>

                        <li class="menu-item @if (Request::is('lottery/*') || Request::is('lottery') || Request::is('lottery_types/*') || Request::is('lottery_types')) menuitem-active @php $selected = 1; @endphp @endif">
                            <a href="{{url('/lottery')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img src="{{url('icons')}}/sorteos{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Sorteos </span>
                                @php $selected = null; @endphp
                            </a>
                        </li>

                        <li class="menu-item @if (Request::is('reserves/*') || Request::is('reserves')) menuitem-active @php $selected = 1; @endphp @endif">
                            <a href="{{url('/reserves')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img src="{{url('icons')}}/reservas{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Reservas </span>
                                @php $selected = null; @endphp
                            </a>
                        </li>

                        <li class="menu-item @if (Request::is('sets/*') || Request::is('sets')) menuitem-active @php $selected = 1; @endphp @endif">
                            <a href="{{url('/sets')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img style="width: 20px;" src="{{url('icons')}}/sets{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Set particip. </span>
                                @php $selected = null; @endphp
                            </a>
                        </li>

                        <li class="menu-item @if (Request::is('design/*') || Request::is('design')) menuitem-active @php $selected = 1; @endphp @endif">
                            <a href="{{url('/design')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img style="width: 20px;" src="{{url('icons')}}/diseno{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Diseño e Impresión </span>
                                @php $selected = null; @endphp
                            </a>
                        </li>

                        <li class="menu-item @if (Request::is('participations/*') || Request::is('participations')) menuitem-active @php $selected = 1; @endphp @endif">
                            <a href="{{url('participations')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img src="{{url('icons')}}/participaciones{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Participaciones </span>
                                @php $selected = null; @endphp
                            </a>
                        </li>

                        <li class="menu-item">
                            <a href="{{url('returns')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img style="width: 19.5px;" src="{{url('icons')}}/devolucion{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Devolución </span>
                            </a>
                        </li>

                        <li class="menu-item @if (Request::is('social/*') || Request::is('social')) menuitem-active @php $selected = 1; @endphp @endif">
                            <a href="{{url('social')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img src="{{url('icons')}}/websocial{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Web Social </span>
                                @php $selected = null; @endphp
                            </a>
                        </li>

                        <li class="menu-item @if (Request::is('requests/*') || Request::is('requests')) menuitem-active @php $selected = 1; @endphp @endif">
                            <a href="{{url('requests')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img src="{{url('icons')}}/solicitudes{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Solicitudes </span>
                                @php $selected = null; @endphp
                            </a>
                        </li>

                        <li class="menu-item @if (Request::is('communications/*') || Request::is('communications')) menuitem-active @php $selected = 1; @endphp @endif">
                            <a href="{{url('communications')}}" class="menu-link">
                                <span class="menu-icon">
                                    <img src="{{url('icons')}}/comunicados{{$selected == 1 ? '_selected' : ''}}.svg" alt="">
                                </span>
                                <span class="menu-text"> Notificaciones </span>
                                @php $selected = null; @endphp
                            </a>
                        </li>


                        {{-- <li class="menu-item">
                            <a href="#menuCrm" data-bs-toggle="collapse" class="menu-link">
                                <span class="menu-icon"><i data-feather="users"></i></span>
                                <span class="menu-text"> CRM </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="menuCrm">
                                <ul class="sub-menu">
                                    <li class="menu-item">
                                        <a href="crm-dashboard.html" class="menu-link">
                                            <span class="menu-text">Dashboard</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="crm-contacts.html" class="menu-link">
                                            <span class="menu-text">Contacts</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="crm-opportunities.html" class="menu-link">
                                            <span class="menu-text">Opportunities</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="crm-leads.html" class="menu-link">
                                            <span class="menu-text">Leads</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="crm-customers.html" class="menu-link">
                                            <span class="menu-text">Customers</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li> --}}

                        
                    </ul>
                    <!--- End Menu -->
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- ========== Left menu End ========== -->

            

            

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">

                <!-- ========== Topbar Start ========== -->
                <div class="navbar-custom">
                    <div class="topbar">
                        <div class="topbar-menu d-flex align-items-center gap-1">

                            <!-- Topbar Brand Logo -->
                            <div class="logo-box">
                                <!-- Brand Logo Light -->
                                <a href="index.html" class="logo-light">
                                    {{-- <img src="{{url('default')}}/assets/images/logo-light.png" alt="logo" class="logo-lg">
                                    <img src="{{url('default')}}/assets/images/logo-sm.png" alt="small logo" class="logo-sm"> --}}

                                    <img src="{{url('/')}}/logo_menu.svg" alt="logo" class="logo-lg">
                                    <img src="{{url('/')}}/logo.svg" alt="small logo" class="logo-sm">
                                </a>

                                <!-- Brand Logo Dark -->
                                <a href="index.html" class="logo-dark">
                                    {{-- <img src="{{url('default')}}/assets/images/logo-dark.png" alt="dark logo" class="logo-lg">
                                    <img src="{{url('default')}}/assets/images/logo-sm.png" alt="small logo" class="logo-sm"> --}}

                                    <img src="{{url('/')}}/logo_menu.svg" alt="logo" class="logo-lg">
                                    <img src="{{url('/')}}/logo.svg" alt="small logo" class="logo-sm">
                                </a>
                            </div>

                            <!-- Sidebar Menu Toggle Button -->
                            <button class="button-toggle-menu">
                                <i class="mdi mdi-menu"></i>
                            </button>
                            
                        </div>

                        <ul class="topbar-menu d-flex align-items-center">
                            <!-- Topbar Search Form -->
                            <li class="app-search dropdown me-3 d-none d-lg-block">
                                <form>
                                    <input type="search" class="form-control rounded-pill" placeholder="Buscar..." id="top-search">
                                    <span class="fe-search search-icon font-16"></span>
                                </form>
                                {{-- <div class="dropdown-menu dropdown-menu-animated dropdown-lg" id="search-dropdown">
                                    <!-- item-->
                                    <div class="dropdown-header noti-title">
                                        <h5 class="text-overflow mb-2">Found 22 results</h5>
                                    </div>

                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="fe-home me-1"></i>
                                        <span>Analytics Report</span>
                                    </a>

                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="fe-aperture me-1"></i>
                                        <span>How can I help you?</span>
                                    </a>

                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="fe-settings me-1"></i>
                                        <span>User profile settings</span>
                                    </a>

                                    <!-- item-->
                                    <div class="dropdown-header noti-title">
                                        <h6 class="text-overflow mb-2 text-uppercase">Users</h6>
                                    </div>

                                    <div class="notification-list">
                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                                            <div class="d-flex align-items-start">
                                                <img class="d-flex me-2 rounded-circle" src="{{url('default')}}/assets/images/users/user-2.jpg" alt="Generic placeholder image" height="32">
                                                <div class="w-100">
                                                    <h5 class="m-0 font-14">Erwin E. Brown</h5>
                                                    <span class="font-12 mb-0">UI Designer</span>
                                                </div>
                                            </div>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                                            <div class="d-flex align-items-start">
                                                <img class="d-flex me-2 rounded-circle" src="{{url('default')}}/assets/images/users/user-5.jpg" alt="Generic placeholder image" height="32">
                                                <div class="w-100">
                                                    <h5 class="m-0 font-14">Jacob Deo</h5>
                                                    <span class="font-12 mb-0">Developer</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div> --}}
                            </li>

                            <!-- Fullscreen Button -->
                            {{-- <li class="d-none d-md-inline-block">
                                <a class="nav-link waves-effect waves-light" href="#" data-toggle="fullscreen">
                                    <i class="fe-maximize font-22"></i>
                                </a>
                            </li> --}}

                            <!-- Search Dropdown (for Mobile/Tablet) -->
                            {{-- <li class="dropdown d-lg-none">
                                <a class="nav-link dropdown-toggle waves-effect waves-light arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <i class="ri-search-line font-22"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                                    <form class="p-3">
                                        <input type="search" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                    </form>
                                </div>
                            </li> --}}

                            {{-- <!-- App Dropdown -->
                            <li class="dropdown d-none d-md-inline-block">
                                <a class="nav-link dropdown-toggle waves-effect waves-light arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <i class="fe-grid font-22"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg p-0">

                                    <div class="p-2">
                                        <div class="row g-0">
                                            <div class="col">
                                                <a class="dropdown-icon-item" href="#">
                                                    <img src="{{url('default')}}/assets/images/brands/slack.png" alt="slack">
                                                    <span>Slack</span>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a class="dropdown-icon-item" href="#">
                                                    <img src="{{url('default')}}/assets/images/brands/github.png" alt="Github">
                                                    <span>GitHub</span>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a class="dropdown-icon-item" href="#">
                                                    <img src="{{url('default')}}/assets/images/brands/dribbble.png" alt="dribbble">
                                                    <span>Dribbble</span>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="row g-0">
                                            <div class="col">
                                                <a class="dropdown-icon-item" href="#">
                                                    <img src="{{url('default')}}/assets/images/brands/bitbucket.png" alt="bitbucket">
                                                    <span>Bitbucket</span>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a class="dropdown-icon-item" href="#">
                                                    <img src="{{url('default')}}/assets/images/brands/dropbox.png" alt="dropbox">
                                                    <span>Dropbox</span>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a class="dropdown-icon-item" href="#">
                                                    <img src="{{url('default')}}/assets/images/brands/g-suite.png" alt="G Suite">
                                                    <span>G Suite</span>
                                                </a>
                                            </div>
                                        </div> <!-- end row-->
                                    </div>
                                </div>
                            </li>

                            <!-- Language flag dropdown  -->
                            <li class="dropdown d-none d-md-inline-block">
                                <a class="nav-link dropdown-toggle waves-effect waves-light arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <img src="{{url('default')}}/assets/images/flags/us.jpg" alt="user-image" class="me-0 me-sm-1" height="18">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated">

                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <img src="{{url('default')}}/assets/images/flags/germany.jpg" alt="user-image" class="me-1" height="12"> <span class="align-middle">German</span>
                                    </a>

                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <img src="{{url('default')}}/assets/images/flags/italy.jpg" alt="user-image" class="me-1" height="12"> <span class="align-middle">Italian</span>
                                    </a>

                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <img src="{{url('default')}}/assets/images/flags/spain.jpg" alt="user-image" class="me-1" height="12"> <span class="align-middle">Spanish</span>
                                    </a>

                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <img src="{{url('default')}}/assets/images/flags/russia.jpg" alt="user-image" class="me-1" height="12"> <span class="align-middle">Russian</span>
                                    </a>

                                </div>
                            </li> --}}

                            <!-- Notofication dropdown -->
                            <li class="dropdown notification-list">
                                <a class="nav-link dropdown-toggle waves-effect waves-light arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <i class="fe-bell font-22"></i>
                                    <span class="badge bg-danger rounded-circle noti-icon-badge">9</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0">
                                    <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-0 font-16 fw-semibold"> Notification</h6>
                                            </div>
                                            <div class="col-auto">
                                                <a href="javascript: void(0);" class="text-dark text-decoration-underline">
                                                    <small>Clear All</small>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-1" style="max-height: 300px;" data-simplebar>

                                        <h5 class="text-muted font-13 fw-normal mt-2">Today</h5>
                                        <!-- item-->

                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card unread-noti shadow-none mb-1">
                                            <div class="card-body">
                                                <span class="float-end noti-close-btn text-muted"><i class="mdi mdi-close"></i></span>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon bg-primary">
                                                            <i class="mdi mdi-comment-account-outline"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold font-14">Datacorp <small class="fw-normal text-muted ms-1">1 min ago</small></h5>
                                                        <small class="noti-item-subtitle text-muted">Caleb Flakelar commented on Admin</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card read-noti shadow-none mb-1">
                                            <div class="card-body">
                                                <span class="float-end noti-close-btn text-muted"><i class="mdi mdi-close"></i></span>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon bg-info">
                                                            <i class="mdi mdi-account-plus"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold font-14">Admin <small class="fw-normal text-muted ms-1">1 hours ago</small></h5>
                                                        <small class="noti-item-subtitle text-muted">New user registered</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <h5 class="text-muted font-13 fw-normal mt-0">Yesterday</h5>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card read-noti shadow-none mb-1">
                                            <div class="card-body">
                                                <span class="float-end noti-close-btn text-muted"><i class="mdi mdi-close"></i></span>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon">
                                                            <img src="{{url('default')}}/assets/images/users/avatar-2.jpg" class="img-fluid rounded-circle" alt="" />
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold font-14">Cristina Pride <small class="fw-normal text-muted ms-1">1 day ago</small></h5>
                                                        <small class="noti-item-subtitle text-muted">Hi, How are you? What about our next meeting</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <h5 class="text-muted font-13 fw-normal mt-0">30 Dec 2021</h5>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card read-noti shadow-none mb-1">
                                            <div class="card-body">
                                                <span class="float-end noti-close-btn text-muted"><i class="mdi mdi-close"></i></span>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon bg-primary">
                                                            <i class="mdi mdi-comment-account-outline"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold font-14">Datacorp</h5>
                                                        <small class="noti-item-subtitle text-muted">Caleb Flakelar commented on Admin</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card read-noti shadow-none mb-1">
                                            <div class="card-body">
                                                <span class="float-end noti-close-btn text-muted"><i class="mdi mdi-close"></i></span>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon">
                                                            <img src="{{url('default')}}/assets/images/users/avatar-4.jpg" class="img-fluid rounded-circle" alt="" />
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold font-14">Karen Robinson</h5>
                                                        <small class="noti-item-subtitle text-muted">Wow ! this admin looks good and awesome design</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <div class="text-center">
                                            <i class="mdi mdi-dots-circle mdi-spin text-muted h3 mt-0"></i>
                                        </div>
                                    </div>

                                    <!-- All-->
                                    <a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item border-top border-light py-2">
                                        View All
                                    </a>

                                </div>
                            </li>

                            <!-- Light/Dark Mode Toggle Button -->
                            {{-- <li class="d-none d-sm-inline-block">
                                <div class="nav-link waves-effect waves-light" id="light-dark-mode">
                                    <i class="ri-moon-line font-22"></i>
                                </div>
                            </li> --}}

                            <!-- Right Bar offcanvas button (Theme Customization Panel) -->
                            <li>
                                <a class="nav-link waves-effect waves-light" data-bs-toggle="offcanvas" href="#theme-settings-offcanvas">
                                    <i class="fe-settings font-22"></i>
                                </a>
                            </li>

                            <!-- User Dropdown -->
                            <li class="dropdown">
                                <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <img src="{{url('default')}}/assets/images/users/user-1.jpg" alt="user-image" class="rounded-circle">
                                    <span class="ms-1 d-none d-md-inline-block">
                                        {{ Auth::user()->name ?? 'Usuario' }} <i class="mdi mdi-chevron-down"></i>
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                                    <!-- item-->
                                    <div class="dropdown-header noti-title">
                                        <h6 class="text-overflow m-0">Welcome !</h6>
                                    </div>

                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="fe-user"></i>
                                        <span>Mi Cuenta</span>
                                    </a>

                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="fe-settings"></i>
                                        <span>Configuración</span>
                                    </a>

                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="fe-lock"></i>
                                        <span>Bloquear Pantalla</span>
                                    </a>

                                    <div class="dropdown-divider"></div>

                                    <!-- item-->
                                    <form method="POST" action="{{ url('logout') }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="dropdown-item notify-item" style="background: none; border: none; width: 100%; text-align: left;">
                                            <i class="fe-log-out me-1"></i>
                                            <span>Cerrar Sesión</span>
                                        </button>
                                    </form>

                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- ========== Topbar End ========== -->

                <div class="content">

                    <div class="container-fluid mt-2" style="margin-bottom: -1.5rem;">

                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                    {{ session('success') }}
                            </div>
                            @php
                                session()->remove('success');
                            @endphp
                        @endif

                        @if (session('warning'))
                            <div class="alert alert-warning" role="alert">
                                    {{ session('warning') }}
                            </div>
                            @php
                                session()->remove('warning');
                            @endphp
                        @endif


                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                    {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->has('error'))
                            <div class="alert alert-danger">
                                {{ $errors->first('error') }}
                            </div>
                        @endif

                    </div>


                    @yield('content')

                </div> <!-- content -->

                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div><script>document.write(new Date().getFullYear())</script> © Partilot - <a href="https://ceroideas.es/" target="_blank">ceroideas.es</a></div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-none d-md-flex gap-4 align-item-center justify-content-md-end footer-links">
                                    <a href="javascript: void(0);">About</a>
                                    <a href="javascript: void(0);">Support</a>
                                    <a href="javascript: void(0);">Contact Us</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

        </div>
        <!-- END wrapper -->

        <!-- Theme Settings -->
        <div class="offcanvas offcanvas-end right-bar" tabindex="-1" id="theme-settings-offcanvas">
            <div class="d-flex align-items-center w-100 p-0 offcanvas-header">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-bordered nav-justified w-100" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link py-2" data-bs-toggle="tab" href="#chat-tab" role="tab">
                            <i class="mdi mdi-message-text d-block font-22 my-1"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-2" data-bs-toggle="tab" href="#tasks-tab" role="tab">
                            <i class="mdi mdi-format-list-checkbox d-block font-22 my-1"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-2 active" data-bs-toggle="tab" href="#settings-tab" role="tab">
                            <i class="mdi mdi-cog-outline d-block font-22 my-1"></i>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="offcanvas-body p-3 h-100" data-simplebar>
                <!-- Tab panes -->
                <div class="tab-content pt-0">
                    <div class="tab-pane" id="chat-tab" role="tabpanel">

                        <form class="search-bar">
                            <div class="position-relative">
                                <input type="text" class="form-control" placeholder="Buscar...">
                                <span class="mdi mdi-magnify"></span>
                            </div>
                        </form>

                        <h6 class="fw-medium mt-2 text-uppercase">Group Chats</h6>

                        <div>
                            <a href="javascript: void(0);" class="text-reset notification-item ps-3 mb-2 d-block">
                                <i class="mdi mdi-checkbox-blank-circle-outline me-1 text-success"></i>
                                <span class="mb-0 mt-1">App Development</span>
                            </a>

                            <a href="javascript: void(0);" class="text-reset notification-item ps-3 mb-2 d-block">
                                <i class="mdi mdi-checkbox-blank-circle-outline me-1 text-warning"></i>
                                <span class="mb-0 mt-1">Office Work</span>
                            </a>

                            <a href="javascript: void(0);" class="text-reset notification-item ps-3 mb-2 d-block">
                                <i class="mdi mdi-checkbox-blank-circle-outline me-1 text-danger"></i>
                                <span class="mb-0 mt-1">Personal Group</span>
                            </a>

                            <a href="javascript: void(0);" class="text-reset notification-item ps-3 d-block">
                                <i class="mdi mdi-checkbox-blank-circle-outline me-1"></i>
                                <span class="mb-0 mt-1">Freelance</span>
                            </a>
                        </div>

                        <h6 class="fw-medium mt-3 text-uppercase">Favourites <a href="javascript: void(0);" class="font-18 text-danger"><i class="float-end mdi mdi-plus-circle"></i></a></h6>

                        <div>
                            <a href="javascript: void(0);" class="text-reset notification-item">
                                <div class="d-flex align-items-start noti-user-item">
                                    <div class="position-relative me-2">
                                        <img src="{{url('default')}}/assets/images/users/user-10.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                        <i class="mdi mdi-circle user-status online"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="mt-0 mb-1 font-14">Andrew Mackie</h6>
                                        <div class="font-13 text-muted">
                                            <p class="mb-0 text-truncate">It will seem like simplified English.</p>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <a href="javascript: void(0);" class="text-reset notification-item">
                                <div class="d-flex align-items-start noti-user-item">
                                    <div class="position-relative me-2">
                                        <img src="{{url('default')}}/assets/images/users/user-1.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                        <i class="mdi mdi-circle user-status away"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="mt-0 mb-1 font-14">Rory Dalyell</h6>
                                        <div class="font-13 text-muted">
                                            <p class="mb-0 text-truncate">To an English person, it will seem like simplified</p>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <a href="javascript: void(0);" class="text-reset notification-item">
                                <div class="d-flex align-items-start noti-user-item">
                                    <div class="position-relative me-2">
                                        <img src="{{url('default')}}/assets/images/users/user-9.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                        <i class="mdi mdi-circle user-status busy"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="mt-0 mb-1 font-14">Jaxon Dunhill</h6>
                                        <div class="font-13 text-muted">
                                            <p class="mb-0 text-truncate">To achieve this, it would be necessary.</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <h6 class="fw-medium mt-3 text-uppercase">Other Chats <a href="javascript: void(0);" class="font-18 text-danger"><i class="float-end mdi mdi-plus-circle"></i></a></h6>

                        <div class="pb-4">
                            <a href="javascript: void(0);" class="text-reset notification-item">
                                <div class="d-flex align-items-start noti-user-item">
                                    <div class="position-relative me-2">
                                        <img src="{{url('default')}}/assets/images/users/user-2.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                        <i class="mdi mdi-circle user-status online"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="mt-0 mb-1 font-14">Jackson Therry</h6>
                                        <div class="font-13 text-muted">
                                            <p class="mb-0 text-truncate">Everyone realizes why a new common language.</p>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <a href="javascript: void(0);" class="text-reset notification-item">
                                <div class="d-flex align-items-start noti-user-item">
                                    <div class="position-relative me-2">
                                        <img src="{{url('default')}}/assets/images/users/user-4.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                        <i class="mdi mdi-circle user-status away"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="mt-0 mb-1 font-14">Charles Deakin</h6>
                                        <div class="font-13 text-muted">
                                            <p class="mb-0 text-truncate">The languages only differ in their grammar.</p>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <a href="javascript: void(0);" class="text-reset notification-item">
                                <div class="d-flex align-items-start noti-user-item">
                                    <div class="position-relative me-2">
                                        <img src="{{url('default')}}/assets/images/users/user-5.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                        <i class="mdi mdi-circle user-status online"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="mt-0 mb-1 font-14">Ryan Salting</h6>
                                        <div class="font-13 text-muted">
                                            <p class="mb-0 text-truncate">If several languages coalesce the grammar of the resulting.</p>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <a href="javascript: void(0);" class="text-reset notification-item">
                                <div class="d-flex align-items-start noti-user-item">
                                    <div class="position-relative me-2">
                                        <img src="{{url('default')}}/assets/images/users/user-6.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                        <i class="mdi mdi-circle user-status online"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="mt-0 mb-1 font-14">Sean Howse</h6>
                                        <div class="font-13 text-muted">
                                            <p class="mb-0 text-truncate">It will seem like simplified English.</p>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <a href="javascript: void(0);" class="text-reset notification-item">
                                <div class="d-flex align-items-start noti-user-item">
                                    <div class="position-relative me-2">
                                        <img src="{{url('default')}}/assets/images/users/user-7.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                        <i class="mdi mdi-circle user-status busy"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="mt-0 mb-1 font-14">Dean Coward</h6>
                                        <div class="font-13 text-muted">
                                            <p class="mb-0 text-truncate">The new common language will be more simple.</p>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <a href="javascript: void(0);" class="text-reset notification-item">
                                <div class="d-flex align-items-start noti-user-item">
                                    <div class="position-relative me-2">
                                        <img src="{{url('default')}}/assets/images/users/user-8.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                        <i class="mdi mdi-circle user-status away"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="mt-0 mb-1 font-14">Hayley East</h6>
                                        <div class="font-13 text-muted">
                                            <p class="mb-0 text-truncate">One could refuse to pay expensive translators.</p>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <div class="text-center mt-3">
                                <a href="javascript:void(0);" class="btn btn-sm btn-white">
                                    <i class="mdi mdi-spin mdi-loading me-2"></i>
                                    Load more
                                </a>
                            </div>
                        </div>

                    </div>

                    <div class="tab-pane" id="tasks-tab" role="tabpanel">
                        <h6 class="fw-medium p-3 m-0 text-uppercase">Working Tasks</h6>
                        <div class="px-2">
                            <a href="javascript: void(0);" class="text-reset item-hovered d-block p-2">
                                <p class="text-muted mb-0">App Development<span class="float-end">75%</span></p>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </a>

                            <a href="javascript: void(0);" class="text-reset item-hovered d-block p-2">
                                <p class="text-muted mb-0">Database Repair<span class="float-end">37%</span></p>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 37%" aria-valuenow="37" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </a>

                            <a href="javascript: void(0);" class="text-reset item-hovered d-block p-2">
                                <p class="text-muted mb-0">Backup Create<span class="float-end">52%</span></p>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 52%" aria-valuenow="52" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </a>
                        </div>

                        <h6 class="fw-medium mb-0 mt-4 text-uppercase">Upcoming Tasks</h6>

                        <div>
                            <a href="javascript: void(0);" class="text-reset item-hovered d-block p-2">
                                <p class="text-muted mb-0">Sales Reporting<span class="float-end">12%</span></p>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 12%" aria-valuenow="12" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </a>

                            <a href="javascript: void(0);" class="text-reset item-hovered d-block p-2">
                                <p class="text-muted mb-0">Redesign Website<span class="float-end">67%</span></p>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 67%" aria-valuenow="67" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </a>

                            <a href="javascript: void(0);" class="text-reset item-hovered d-block p-2">
                                <p class="text-muted mb-0">New Admin Design<span class="float-end">84%</span></p>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 84%" aria-valuenow="84" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </a>
                        </div>

                        <div class="p-3 mt-2 d-grid">
                            <a href="javascript: void(0);" class="btn btn-success waves-effect waves-light">Create Task</a>
                        </div>

                    </div>

                    <div class="tab-pane active" id="settings-tab" role="tabpanel">

                        <div class="mt-n3">
                            <h6 class="fw-medium py-2 px-3 font-13 text-uppercase bg-light mx-n3 mt-n3 mb-3">
                                <span class="d-block py-1">Theme Settings</span>
                            </h6>
                        </div>

                        <div class="alert alert-warning" role="alert">
                            <strong>Customize </strong> the overall color scheme, sidebar menu, etc.
                        </div>

                        <h5 class="fw-medium font-14 mt-4 mb-2 pb-1">Color Scheme</h5>

                        <div class="colorscheme-cardradio">
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-bs-theme" id="layout-color-light" value="light">
                                    <label class="form-check-label" for="layout-color-light">Light</label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-bs-theme" id="layout-color-dark" value="dark">
                                    <label class="form-check-label" for="layout-color-dark">Dark</label>
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-medium font-14 mt-4 mb-2 pb-1">Content Width</h5>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="data-layout-width" id="layout-width-default" value="default">
                                <label class="form-check-label" for="layout-width-default">Fluid (Default)</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="data-layout-width" id="layout-width-boxed" value="boxed">
                                <label class="form-check-label" for="layout-width-boxed">Boxed</label>
                            </div>
                        </div>

                        <div id="layout-mode">
                            <h5 class="fw-medium font-14 mt-4 mb-2 pb-1">Layout Mode</h5>

                            <div class="d-flex flex-column gap-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-layout-mode" id="layout-mode-default" value="default">
                                    <label class="form-check-label" for="layout-mode-default">Default</label>
                                </div>


                                <div id="layout-detached">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="data-layout-mode" id="layout-mode-detached" value="detached">
                                        <label class="form-check-label" for="layout-mode-detached">Detached</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-medium font-14 mt-4 mb-2 pb-1">Topbar Color</h5>

                        <div class="d-flex flex-column gap-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="data-topbar-color" id="topbar-color-light" value="light">
                                <label class="form-check-label" for="topbar-color-light">Light</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="data-topbar-color" id="topbar-color-dark" value="dark">
                                <label class="form-check-label" for="topbar-color-dark">Dark</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="data-topbar-color" id="topbar-color-brand" value="brand">
                                <label class="form-check-label" for="topbar-color-brand">Brand</label>
                            </div>
                        </div>

                        <div>
                            <h5 class="fw-medium font-14 mt-4 mb-2 pb-1">Menu Color</h5>

                            <div class="d-flex flex-column gap-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-menu-color" id="leftbar-color-light" value="light">
                                    <label class="form-check-label" for="leftbar-color-light">Light</label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-menu-color" id="leftbar-color-dark" value="dark">
                                    <label class="form-check-label" for="leftbar-color-dark">Dark</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-menu-color" id="leftbar-color-brand" value="brand">
                                    <label class="form-check-label" for="leftbar-color-brand">Brand</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-menu-color" id="leftbar-color-gradient" value="gradient">
                                    <label class="form-check-label" for="leftbar-color-gradient">Gradient</label>
                                </div>
                            </div>
                        </div>

                        <div id="menu-icon-color">
                            <h5 class="fw-medium font-14 mt-4 mb-2 pb-1">Menu Icon Color</h5>

                            <div class="d-flex flex-column gap-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-two-column-color" id="twocolumn-menu-color-light" value="light">
                                    <label class="form-check-label" for="twocolumn-menu-color-light">Light</label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-two-column-color" id="twocolumn-menu-color-dark" value="dark">
                                    <label class="form-check-label" for="twocolumn-menu-color-dark">Dark</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-two-column-color" id="twocolumn-menu-color-brand" value="brand">
                                    <label class="form-check-label" for="twocolumn-menu-color-brand">Brand</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-two-column-color" id="twocolumn-menu-color-gradient" value="gradient">
                                    <label class="form-check-label" for="twocolumn-menu-color-gradient">Gradient</label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h5 class="fw-medium font-14 mt-4 mb-2 pb-1">Menu Icon Tone</h5>

                            <div class="d-flex flex-column gap-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-menu-icon" id="menu-icon-default" value="default">
                                    <label class="form-check-label" for="menu-icon-default">Default</label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-menu-icon" id="menu-icon-twotone" value="twotones">
                                    <label class="form-check-label" for="menu-icon-twotone">Twotone</label>
                                </div>
                            </div>
                        </div>

                        <div id="sidebar-size">
                            <h5 class="fw-medium font-14 mt-4 mb-2 pb-1">Sidebar Size</h5>

                            <div class="d-flex flex-column gap-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-sidenav-size" id="leftbar-size-default" value="default">
                                    <label class="form-check-label" for="leftbar-size-default">Default</label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-sidenav-size" id="leftbar-size-compact" value="compact">
                                    <label class="form-check-label" for="leftbar-size-compact">Compact (Medium Width)</label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-sidenav-size" id="leftbar-size-small" value="condensed">
                                    <label class="form-check-label" for="leftbar-size-small">Condensed (Icon View)</label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-sidenav-size" id="leftbar-size-full" value="full">
                                    <label class="form-check-label" for="leftbar-size-full">Full Layout</label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="data-sidenav-size" id="leftbar-size-fullscreen" value="fullscreen">
                                    <label class="form-check-label" for="leftbar-size-fullscreen">Fullscreen Layout</label>
                                </div>
                            </div>
                        </div>

                        <div id="sidebar-user">
                            <h5 class="fw-medium font-14 mt-4 mb-2 pb-1">Sidebar User Info</h5>

                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="data-sidebar-user" id="sidebaruser-check">
                                <label class="form-check-label" for="sidebaruser-check">Enable</label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="offcanvas-footer border-top py-2 px-2 text-center">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light w-50" onclick="resetThemeSettings()">Reset</button>
                    <button type="button" class="btn btn-primary w-50" onclick="location.reload()">Aplicar</button>
                </div>
            </div>
        </div>
        
        <!-- Vendor js -->
        <script src="{{url('default')}}/assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="{{url('default')}}/assets/js/app.min.js"></script>
        
        <!-- Plugins js-->
        <script src="{{url('assets')}}/libs/flatpickr/flatpickr.min.js"></script>
        <script src="{{url('assets')}}/libs/apexcharts/apexcharts.min.js"></script>
        <script src="{{url('assets')}}/libs/selectize/js/standalone/selectize.min.js"></script>

        <!-- Dashboar 1 init js-->
        <script src="{{url('default')}}/assets/js/pages/dashboard-1.init.js"></script>


        {{-- /**/ --}}

        <!-- third party js -->
        <script src="{{url('assets')}}/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="{{url('assets')}}/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
        <script src="{{url('assets')}}/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="{{url('assets')}}/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
        <script src="{{url('assets')}}/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="{{url('assets')}}/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
        <script src="{{url('assets')}}/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="{{url('assets')}}/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="{{url('assets')}}/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="{{url('assets')}}/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="{{url('assets')}}/libs/datatables.net-select/js/dataTables.select.min.js"></script>
        <script src="{{url('assets')}}/libs/pdfmake/build/pdfmake.min.js"></script>
        <script src="{{url('assets')}}/libs/pdfmake/build/vfs_fonts.js"></script>

        <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
        <!-- third party js ends -->

        <!-- Datatables init -->
        <script src="{{url('default')}}/assets/js/pages/datatables.init.js"></script>

        <script src="https://cdn.ckeditor.com/ckeditor5/45.2.0/ckeditor5.umd.js" crossorigin></script>
        <script src="https://cdn.ckeditor.com/ckeditor5/45.2.0/translations/es.umd.js" crossorigin></script>
        <script src="{{url('main.js')}}"></script>

        @yield('scripts')

    </body>

<!-- Mirrored from coderthemes.com/ubold/layouts/default/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 25 May 2025 15:57:16 GMT -->
</html>