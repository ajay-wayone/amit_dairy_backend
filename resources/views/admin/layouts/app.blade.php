<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Admin Dashboard - Amit Dairy & Sweets')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/assets/images/logo.webp') }}">

    <!-- jsvectormap css -->
    <link href="{{ asset('assets/assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="{{ asset('assets/assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="{{ asset('assets/assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />

    <style>
        :root {
            --sidebar-width: 240px;
            --sidebar-collapsed-width: 70px;
            --topbar-height: 70px;
        }

        .nav-link[aria-expanded="true"] .ri-arrow-down-s-line {
            transform: rotate(180deg);
        }

        .nav-link .ri-arrow-down-s-line {
            transition: transform 0.2s ease-in-out;
        }

        .table-responsive {
            font-size: 0.875rem;
        }

        .search-results {
            max-height: 300px;
            overflow-y: auto;
        }

        /* Sidebar optimizations */
        .app-menu {
            width: var(--sidebar-width) !important;
            max-width: var(--sidebar-width) !important;
            height: 100vh !important;
            position: fixed;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .app-menu .navbar-nav {
            font-size: 0.8rem !important;
        }

        .app-menu .nav-link {
            font-size: 0.9rem !important;
            padding: 0.5rem 1rem !important;
            display: flex;
            align-items: center;
            white-space: nowrap;
        }

        .app-menu .nav-link i {
            font-size: 0.9rem !important;
            margin-right: 0.5rem !important;
            min-width: 20px;
            text-align: center;
        }

        .app-menu .menu-dropdown .nav-link {
            font-size: 0.75rem !important;
            padding: 0.4rem 1.5rem !important;
        }

        .app-menu .menu-title {
            font-size: 1.9rem !important;
            padding: 0.5rem 1rem !important;
        }

        .navbar-brand-box .logo img {
            max-width: 80px !important;
            height: auto !important;
        }

        .main-content {
            margin-left: var(--sidebar-width) !important;
            transition: margin-left 0.3s ease;
        }

        /* Collapsed sidebar */
        body.vertical-collpsed .app-menu {
            width: var(--sidebar-collapsed-width) !important;
            max-width: var(--sidebar-collapsed-width) !important;
        }

        body.vertical-collpsed .main-content {
            margin-left: var(--sidebar-collapsed-width) !important;
        }

        body.vertical-collpsed .logo-lg,
        body.vertical-collpsed .menu-title,
        body.vertical-collpsed .nav-link span:not(.badge) {
            display: none !important;
        }

        body.vertical-collpsed .navbar-brand-box {
            padding: 0;
        }

        body.vertical-collpsed .navbar-brand-box .logo-sm {
            display: block !important;
            margin: 0 auto;
        }

        /* Scrollbar styling for sidebar - HIDDEN but scrollable */
        #scrollbar {
            height: calc(100vh - var(--topbar-height));
            overflow-y: auto;
            padding-bottom: 20px;
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE and Edge */
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        #scrollbar::-webkit-scrollbar {
            display: none;
            width: 0 !important;
        }

        /* Improved notifications */
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 6px;
            z-index: 9999;
            display: none;
            max-width: 350px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideInRight 0.3s ease;
        }

        .notification-toast.hide {
            animation: slideOutRight 0.3s ease;
        }

        .notification-toast.success {
            background: #198754;
            color: #fff;
        }

        .notification-toast.warning {
            background: #ffc107;
            color: #333;
        }

        .notification-toast a {
            color: inherit;
            text-decoration: underline;
            font-weight: 500;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Mobile optimizations */
        @media (max-width: 991.98px) {
            .app-menu {
                width: 100% !important;
                height: auto !important;
                transform: translateX(-100%);
                z-index: 1001;
            }

            .app-menu.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }

            #scrollbar {
                height: auto;
                max-height: 70vh;
            }

            .vertical-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1000;
            }

            .vertical-overlay.show {
                display: block;
            }
        }

        /* Loading indicator for async operations */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Audio elements for notifications -->
    <audio id="orderAlertSound" src="{{ asset('assets/assets/alert.mp3') }}" preload="auto"></audio>
    <audio id="subscriptionAlertSound" src="{{ asset('assets/assets/alert.mp3') }}" preload="auto"></audio>

    <!-- Notification Toasts -->
    <div class="notification-toast success" id="order-toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex align-items-center">
            <span class="me-2">🛒</span>
            <div class="flex-grow-1">
                <strong>New order received!</strong>
                <div>
                    <a href="{{ route('admin.orders.index') }}">View</a>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white ms-2" onclick="hideToast('order-toast')" aria-label="Close"></button>
        </div>
    </div>

    <div class="notification-toast warning" id="subscription-toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex align-items-center">
            <span class="me-2">📝</span>
            <div class="flex-grow-1">
                <strong>New subscription received!</strong>
                <div>
                    <a href="{{ route('admin.subscriptions.index') }}">View</a>
                </div>
            </div>
            <button type="button" class="btn-close ms-2" onclick="hideToast('subscription-toast')" aria-label="Close"></button>
        </div>
    </div>

    <!-- Begin page -->
    <div id="layout-wrapper">
        <!-- Topbar Start -->
        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/assets/images/logo.webp') }}" alt="Amit Dairy & Sweets" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('assets/assets/images/logo.webp') }}" alt="Amit Dairy & Sweets" height="17">
                                </span>
                            </a>

                            <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/assets/images/logo.webp') }}" alt="Amit Dairy & Sweets" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('assets/assets/images/logo.webp') }}" alt="Amit Dairy & Sweets" height="17">
                                </span>
                            </a>
                        </div>

                        <button type="button"
                            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                            id="topnav-hamburger-icon" aria-label="Toggle menu">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false" aria-label="User menu">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user"
                                        src="{{ asset('assets/assets/images/users/avatar-1.jpg') }}"
                                        alt="User Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span
                                            class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ Auth::guard('admin')->user()->name }}</span>
                                        <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Admin</span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="page-header-user-dropdown">
                                <h6 class="dropdown-header">Welcome {{ Auth::guard('admin')->user()->name }}!</h6>
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="mdi mdi-speedometer text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle">Dashboard</span>
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.change-credentials') }}">
                                    <i class="mdi mdi-key-outline text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle">Change Credentials</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                                        <span class="align-middle">Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Topbar End -->

        <!-- Left Sidebar Start -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/assets/images/logo.webp') }}" alt="Amit Dairy & Sweets" height="100" width="100">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/assets/images/logo.webp') }}" alt="Amit Dairy & Sweets" height="100" width="100">
                    </span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/assets/images/logo.webp') }}" alt="Amit Dairy & Sweets" height="100" width="100">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/assets/images/logo.webp') }}" alt="Amit Dairy & Sweets" height="100" width="100">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover" aria-label="Toggle sidebar">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">
                    <div id="two-column-menu"></div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.dashboard') }}">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-widgets">Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#manageMembers" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="manageMembers">
                                <i class="ri-pages-line"></i> <span data-key="t-pages">Manage Users</span>
                                <i class="ri-arrow-down-s-line ms-auto"></i>
                            </a>
                            <div class="collapse menu-dropdown" id="manageMembers">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.customers.index') }}" class="nav-link"
                                            data-key="t-starter">Customer</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.contacts.index') }}" class="nav-link"
                                            data-key="t-team">Contact Us Enquiries</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#pageContent" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="pageContent">
                                <i class="ri-rocket-line"></i> <span data-key="t-landing">Page Content</span>
                                <i class="ri-arrow-down-s-line ms-auto"></i>
                            </a>
                            <div class="collapse menu-dropdown" id="pageContent">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.testimonials.index') }}" class="nav-link"
                                            data-key="t-one-page">Testimonials</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.banners.index') }}" class="nav-link"
                                            data-key="t-one-page">Banners</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.policies.index') }}" class="nav-link"
                                            data-key="t-job">All Policies</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.policies.create') }}" class="nav-link"
                                            data-key="t-job">Create Policy</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.faqs.index') }}" class="nav-link"
                                            data-key="t-job">FAQs</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#orders" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="orders">
                                <i class="ri-rocket-line"></i> <span data-key="t-landing">Orders</span>
                                <i class="ri-arrow-down-s-line ms-auto"></i>
                            </a>
                            <div class="collapse menu-dropdown" id="orders">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.orders.index') }}" class="nav-link"
                                            data-key="t-nft-landing">Orders List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.orders.ready') }}" class="nav-link"
                                            data-key="t-job">Ready To Dispatch</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.orders.cancelled') }}" class="nav-link"
                                            data-key="t-job">Cancelled</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.orders.delivered') }}" class="nav-link"
                                            data-key="t-job">Delivered</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#product" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="product">
                                <i class="ri-stack-line"></i> <span data-key="t-advance-ui">Product</span>
                                <i class="ri-arrow-down-s-line ms-auto"></i>
                            </a>
                            <div class="collapse menu-dropdown" id="product">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.categories.index') }}" class="nav-link"
                                            data-key="t-sweet-alerts">Category</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.subcategories.index') }}" class="nav-link"
                                            data-key="t-nestable-list">Sub category</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.products.index') }}" class="nav-link"
                                            data-key="t-nestable-list">Products</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.payments.index') }}" class="nav-link">
                                            Advance Payments
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{ route('admin.boxes.index') }}" class="nav-link"
                                            data-key="t-nestable-list">Boxes</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('block.index') }}" class="nav-link"
                                            data-key="t-nestable-list">Block Management</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#manageSubscriptionMenu" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="manageSubscriptionMenu">
                                <i class="ri-pages-line"></i>
                                <span data-key="t-pages">Manage Subscription</span>
                                <i class="ri-arrow-down-s-line ms-auto"></i>
                            </a>
                            <div class="collapse menu-dropdown" id="manageSubscriptionMenu">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.subscriptions.index') }}" class="nav-link"
                                            data-key="t-starter">Admin Subscription</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.newsletters.index') }}" class="nav-link"
                                            data-key="t-team">Newsletter Subscription List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.subscriptions.list') }}" class="nav-link"
                                            data-key="t-team">User Subscription List</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.contact-details.index') }}">
                                <i class="ri-contacts-book-3-line"></i> <span data-key="t-advance-ui">Website
                                    Settings</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.gateways.index') }}">
                                <i class="ri-key-2-line"></i> <span data-key="t-advance-ui">Gateway Settings</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.smtp.*') ? 'active' : '' }}" href="{{ route('admin.smtp.edit') }}">
                                <i class="ri-mail-settings-line"></i> <span data-key="t-advance-ui">Configure SMTP</span>
                            </a>
                        </li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.offer.*') ? 'active' : '' }}" 
       href="{{ route('admin.offer.create') }}">
        <i class="ri-price-tag-3-line"></i>
        <span data-key="t-offer">Offer Settings</span>
    </a>
</li>






                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.delivery-locations.index') }}">
                                <i class="ri-focus-3-line"></i> <span data-key="t-advance-ui">Delivery Locations</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.change-credentials') }}">
                                <i class="mdi mdi-key-outline"></i> <span data-key="t-advance-ui">Change
                                    Credentials</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link p-0 text-start w-100"
                                    style="border: none; background: none;">
                                    <i class="ri-logout-box-r-line"></i>
                                    <span data-key="t-advance-ui">Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->

        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> Amit Dairy & Sweets.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Crafted with <i class="mdi mdi-heart text-danger"></i> by Wayone It solutions
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/assets/js/plugins.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/admin-delete.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Mobile sidebar toggle
            $('#topnav-hamburger-icon').on('click', function () {
                if ($(window).width() < 992) {
                    $('.app-menu').toggleClass('mobile-open');
                    $('.vertical-overlay').toggleClass('show');
                } else {
                    $('body').toggleClass('vertical-collpsed');
                }
            });

            // Close sidebar on mobile when clicking outside
            $('.vertical-overlay').on('click', function () {
                $('.app-menu').removeClass('mobile-open');
                $(this).removeClass('show');
            });

            // Close sidebar on mobile when clicking a link
            $(document).on('click', '.app-menu .nav-link', function() {
                if ($(window).width() < 992) {
                    $('.app-menu').removeClass('mobile-open');
                    $('.vertical-overlay').removeClass('show');
                }
            });

            // Submenu toggle for mobile
            $('.menu-link[data-bs-toggle="collapse"]').on('click', function (e) {
                if ($(window).width() < 992) {
                    const target = $($(this).attr('href'));
                    const isExpanded = $(this).attr('aria-expanded') === 'true';
                    
                    // Close all other open dropdowns
                    $('.menu-dropdown').not(target).collapse('hide');
                    $('.menu-link[data-bs-toggle="collapse"]').not(this).attr('aria-expanded', 'false');
                    
                    if (!isExpanded) {
                        e.preventDefault();
                        target.collapse('show');
                        $(this).attr('aria-expanded', 'true');
                    }
                }
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Notification System
        let lastOrderId = 0;
        let lastSubscriptionId = 0;
        let isChecking = false;

        function playSound(type = 'order') {
            const sound = document.getElementById(`${type}AlertSound`);
            if (sound) {
                sound.pause();
                sound.currentTime = 0;
                sound.play().catch(err => console.warn("Autoplay blocked:", err));
            }
        }

        function showToast(type = 'order') {
            const toast = document.getElementById(`${type}-toast`);
            toast.style.display = 'block';
            toast.classList.remove('hide');
            
            setTimeout(() => {
                hideToast(`${type}-toast`);
            }, 5000);
        }

        function hideToast(toastId) {
            const toast = document.getElementById(toastId);
            toast.classList.add('hide');
            setTimeout(() => {
                toast.style.display = 'none';
                toast.classList.remove('hide');
            }, 300);
        }

        async function checkNewData(endpoint, type) {
            if (isChecking) return;
            
            isChecking = true;
            try {
                const response = await fetch(endpoint + '?check_new=1');
                const data = await response.json();
                
                if (!data || typeof data.latest_id === 'undefined' || isNaN(parseInt(data.latest_id))) return;
                
                const newId = parseInt(data.latest_id);
                const lastId = type === 'order' ? lastOrderId : lastSubscriptionId;

                if (newId > lastId) {
                    console.log(`${type} notification triggered for ID:`, newId);
                    playSound(type);
                    showToast(type);

                    if ('speechSynthesis' in window) {
                        const msg = new SpeechSynthesisUtterance(
                            `New ${type} received on Amit Dairy and Sweets.`);
                        msg.lang = 'en-IN';
                        window.speechSynthesis.speak(msg);
                    }

                    if (type === 'order') {
                        lastOrderId = newId;
                    } else {
                        lastSubscriptionId = newId;
                    }
                }
            } catch (err) {
                console.warn('Failed to check for new data:', err);
            } finally {
                isChecking = false;
            }
        }

        // Check every 2 seconds for orders, 3 seconds for subscriptions
        setInterval(() => checkNewData('{{ route('admin.orders.index') }}', 'order'), 2000);
        setInterval(() => checkNewData('{{ route('admin.subscriptions.index') }}', 'subscription'), 3000);
    </script>

    @stack('scripts')
</body>

</html>