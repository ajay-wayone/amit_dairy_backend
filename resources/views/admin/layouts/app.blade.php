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
        .nav-link[aria-expanded="true"] .ri-arrow-down-s-line {
            transform: rotate(180deg);
        }

        .nav-link .ri-arrow-down-s-line {
            transition: transform 0.2s;
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
            width: 240px !important;
            max-width: 240px !important;
        }

        .app-menu .navbar-nav {
            font-size: 0.8rem !important;
        }

        .app-menu .nav-link {
            font-size: .9rem !important;
            padding: 0.5rem 1rem !important;
        }

        .app-menu .nav-link i {
            font-size: 0.9rem !important;
            margin-right: 0.5rem !important;
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
            margin-left: 240px !important;
        }

        @media (max-width: 991.98px) {
            .app-menu {
                width: 100% !important;
            }

            .main-content {
                margin-left: 0 !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Audio element for notifications -->
    <audio id="orderAlertSound" src="{{ asset('assets/assets/alert.mp3') }}" preload="auto"></audio>
    <audio id="subscriptionAlertSound" src="{{ asset('assets/assets/alert.mp3') }}" preload="auto"></audio>

    <!-- Order Notification Toast -->
    <div id="order-toast"
        style="display:none; position:fixed; top:20px; right:20px; background:#198754; color:#fff; padding:12px 20px; border-radius:6px; z-index:9999;">
        🛒 <strong>New order received!</strong>
        <a href="{{ route('admin.orders.index') }}" style="color:#fff; text-decoration:underline;">View</a>
    </div>

    <!-- Subscription Notification Toast -->
    <div id="subscription-toast"
        style="display:none; position:fixed; top:60px; right:20px; background:#ffc107; color:#333; padding:12px 20px; border-radius:6px; z-index:9999;">
        📝 <strong>New subscription received!</strong>
        <a href="{{ route('admin.subscriptions.index') }}" style="color:#333; text-decoration:underline;">View</a>
    </div>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- ========== Topbar Start ========== -->
        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/assets/images/logo.webp') }}" alt=""
                                        height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('assets/assets/images/logo.webp') }}" alt=""
                                        height="17">
                                </span>
                            </a>

                            <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/assets/images/logo.webp') }}" alt=""
                                        height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('assets/assets/images/logo.webp') }}" alt=""
                                        height="17">
                                </span>
                            </a>
                        </div>

                        <button type="button"
                            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                            id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user"
                                        src="{{ asset('assets/assets/images/users/avatar-1.jpg') }}"
                                        alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span
                                            class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ Auth::guard('admin')->user()->name }}</span>
                                        <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Admin</span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
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
        <!-- ========== Topbar End ========== -->

        <!-- ========== Left Sidebar Start ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/assets/images/logo.webp') }}" alt="" height="100px"
                            width="100px">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/assets/images/logo.webp') }}" alt="" height="10px"
                            width="100px">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/assets/images/logo.webp') }}" alt="" height="100px"
                            width="100px">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/assets/images/logo.webp') }}" alt="" height="100px"
                            width="100px">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
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
                            <a class="nav-link menu-link" href="#manageMembers" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="manageMembers">
                                <i class="ri-pages-line"></i> <span data-key="t-pages">Manage Members</span>
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
                            <a class="nav-link menu-link" href="#pageContent" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="pageContent">
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
                                        <a href="{{ route('admin.policies.disclaimer') }}" class="nav-link"
                                            data-key="t-job">Disclaimer</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.policies.terms') }}" class="nav-link"
                                            data-key="t-job">Terms & Condition</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.policies.privacy') }}" class="nav-link"
                                            data-key="t-job">Privacy Policy</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.policies.refund') }}" class="nav-link"
                                            data-key="t-job">Refund Policy</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.policies.return') }}" class="nav-link"
                                            data-key="t-job">Return Policy</a>
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
                                        <a href="{{ route('admin.orders.new') }}" class="nav-link"
                                            data-key="t-one-page">New Orders</a>
                                    </li>
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
                                        <a href="{{ route('admin.boxes.index') }}" class="nav-link"
                                            data-key="t-nestable-list">Boxes</a>
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
                            <a class="nav-link" href="{{ route('admin.contact-details') }}">
                                <i class="ri-contacts-book-3-line"></i> <span data-key="t-advance-ui">Website
                                    Settings</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.delivery-locations.index') }}">
                                <i class="ri-focus-3-line"></i> <span data-key="t-advance-ui">Delivery
                                    Locations</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.change-credentials') }}">
                                <i class="mdi mdi-key-outline"></i> <span data-key="t-advance-ui">Change
                                    Credentials</span>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.logout') }}">
                                <i class="ri-logout-box-r-line"></i> <span data-key="t-advance-ui">Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->

        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
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
                            <script>
                                document.write(new Date().getFullYear())
                            </script> © Amit Dairy & Sweets.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Crafted with <i class="mdi mdi-heart text-danger"></i> by Amit Dairy & Sweets
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

    <!-- Notification System -->
    <script>
        let lastOrderId = 0;
        let lastSubscriptionId = 0;

        function playSound() {
            const sound = document.getElementById('orderAlertSound');
            if (sound) {
                sound.pause();
                sound.currentTime = 0;
                sound.play().catch(err => console.warn("Autoplay blocked:", err));
            }
        }

        function showPopup() {
            const popup = document.getElementById('order-toast');
            popup.style.display = 'block';
            setTimeout(() => {
                popup.style.display = 'none';
            }, 4000);
        }

        function playSubscriptionSound() {
            const sound = document.getElementById('subscriptionAlertSound');
            if (sound) {
                sound.pause();
                sound.currentTime = 0;
                sound.play().catch(err => console.warn("Autoplay blocked:", err));
            }
        }

        function showSubscriptionPopup() {
            const popup = document.getElementById('subscription-toast');
            popup.style.display = 'block';
            setTimeout(() => {
                popup.style.display = 'none';
            }, 4000);
        }

        function checkNewOrders() {
            fetch('{{ route('admin.orders.index') }}?check_new=1')
                .then(res => res.json())
                .then(data => {
                    if (!data || typeof data.latest_id === 'undefined' || isNaN(parseInt(data.latest_id))) return;
                    const newId = parseInt(data.latest_id);
                    if (newId > lastOrderId) {
                        console.log('Order notification triggered for order ID:', newId);
                        playSound();
                        showPopup();
                        if ('speechSynthesis' in window) {
                            const msg = new SpeechSynthesisUtterance('New order received on Amit Dairy and Sweets.');
                            msg.lang = 'en-IN';
                            window.speechSynthesis.speak(msg);
                        }
                        lastOrderId = newId;
                    }
                })
                .catch(err => {
                    /* silent fail */
                });
        }

        function checkNewSubscriptions() {
            fetch('{{ route('admin.subscriptions.index') }}?check_new=1')
                .then(res => res.json())
                .then(data => {
                    if (!data || typeof data.latest_id === 'undefined' || isNaN(parseInt(data.latest_id))) return;
                    const newId = parseInt(data.latest_id);
                    if (newId > lastSubscriptionId) {
                        console.log('Subscription notification triggered for subscription ID:', newId);
                        showSubscriptionPopup();
                        if ('speechSynthesis' in window) {
                            const msg = new SpeechSynthesisUtterance(
                                'New subscription received on Amit Dairy and Sweets.');
                            msg.lang = 'en-IN';
                            window.speechSynthesis.speak(msg);
                        }
                        lastSubscriptionId = newId;
                    }
                })
                .catch(err => {
                    /* silent fail */
                });
        }

        // Check every 2 seconds for orders, 3 seconds for subscriptions
        setInterval(checkNewOrders, 2000);
        setInterval(checkNewSubscriptions, 3000);
    </script>

    @stack('scripts')
</body>

</html>
