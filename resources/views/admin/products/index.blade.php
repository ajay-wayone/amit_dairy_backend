@extends('admin.layouts.app')

@section('title', 'Products - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Products</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="card-title mb-0">Manage Products</h4>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                    <i class="ri-add-line align-bottom me-1"></i> Add Product
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="search-box">
                                <input type="text" class="form-control" id="searchInput"
                                    placeholder="Search products...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="categoryFilter">
                                <option value="">All Categories</option>
                                @foreach ($categories ?? [] as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary w-100" id="clearFilters">
                                <i class="ri-refresh-line"></i> Clear
                            </button>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="table-responsive">
                        <div id="productsTable">
                            @include('admin.products.partials.table')
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* Improved Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .pagination .page-item {
            margin: 2px;
        }

        .pagination .page-link {
            color: #495057;
            border: 1px solid #e0e3eb;
            border-radius: 6px;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: 500;
            min-width: 40px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .pagination .page-link:hover {
            background-color: #f5f6f8;
            border-color: #d8dbe0;
            transform: translateY(-1px);
        }

        .pagination .page-item.active .page-link {
            background-color: #405189;
            border-color: #405189;
            color: white;
            box-shadow: 0 2px 5px rgba(64, 81, 137, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            background-color: #f8f9fa;
            border-color: #e0e3eb;
            box-shadow: none;
        }

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            border-radius: 6px;
            padding: 8px 12px;
        }

        .pagination .page-item .page-link i {
            font-size: 16px;
            line-height: 1;
        }

        .pagination-info {
            text-align: center;
            margin-top: 15px;
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }

        /* Loading indicator for pagination */
        .pagination-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }

        .pagination-loading:after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid #405189;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .search-box {
            position: relative;
        }

        .search-box .search-icon {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #98a6ad;
        }

        .search-box .form-control {
            padding-right: 40px;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .featured-badge {
            background-color: #fff3cd;
            color: #856404;
        }

        .action-buttons .btn {
            padding: 4px 8px;
            font-size: 12px;
            margin-right: 4px;
        }

        .price {
            font-weight: 600;
            color: #28a745;
        }

        .sale-price {
            color: #dc3545;
            text-decoration: line-through;
            font-size: 0.9em;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let searchTimer;

            // AJAX Search and Filters
            function performSearch() {
                const searchTerm = $('#searchInput').val();
                const categoryId = $('#categoryFilter').val();
                const status = $('#statusFilter').val();

                $.ajax({
                    url: '{{ route('admin.products.index') }}',
                    type: 'GET',
                    data: {
                        search: searchTerm,
                        category_id: categoryId,
                        status: status
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        $('#productsTable').html(response.html);
                        if (response.pagination) {
                            $('.pagination-container').html(response.pagination);
                        }
                    },
                    error: function() {
                        console.log('Search failed');
                    }
                });
            }

            $('#searchInput').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(performSearch, 500);
            });

            $('#categoryFilter, #statusFilter').on('change', function() {
                performSearch();
            });

            $('#clearFilters').on('click', function() {
                $('#searchInput').val('');
                $('#categoryFilter').val('');
                $('#statusFilter').val('');
                performSearch();
            });

            // Toggle Status
            $(document).on('click', '.toggle-status', function() {
                const productId = $(this).data('id');
                const button = $(this);

                $.ajax({
                    url: `/admin/products/${productId}/toggle-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.is_active) {
                                button.removeClass('btn-warning').addClass('btn-success');
                                button.html('<i class="ri-check-line"></i> Active');
                            } else {
                                button.removeClass('btn-success').addClass('btn-warning');
                                button.html('<i class="ri-close-line"></i> Inactive');
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to update status'
                        });
                    }
                });
            });

            // Toggle Featured
            $(document).on('click', '.toggle-featured', function() {
                const productId = $(this).data('id');
                const button = $(this);

                $.ajax({
                    url: `/admin/products/${productId}/toggle-featured`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.is_featured) {
                                button.removeClass('btn-outline-warning').addClass(
                                    'btn-warning');
                                button.html('<i class="ri-star-fill"></i> Featured');
                            } else {
                                button.removeClass('btn-warning').addClass(
                                    'btn-outline-warning');
                                button.html('<i class="ri-star-line"></i> Not Featured');
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to update featured status'
                        });
                    }
                });
            });

            // Delete Product
            $(document).on('click', '.delete-product', function() {
                const productId = $(this).data('id');
                const productName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete "${productName}"? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = $('<form>', {
                            'method': 'POST',
                            'action': `/admin/products/${productId}`
                        });

                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': '{{ csrf_token() }}'
                        }));

                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_method',
                            'value': 'DELETE'
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });

            // Pagination
            // Enhanced Pagination with Loading State
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();

                // Add loading state
                const paginationContainer = $(this).closest('.pagination');
                paginationContainer.addClass('pagination-loading');

                const page = $(this).attr('href').split('page=')[1];
                const searchTerm = $('#searchInput').val();
                const categoryId = $('#categoryFilter').val();
                const status = $('#statusFilter').val();

                $.ajax({
                    url: '{{ route('admin.products.index') }}',
                    type: 'GET',
                    data: {
                        page: page,
                        search: searchTerm,
                        category_id: categoryId,
                        status: status
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        $('#productsTable').html(response.html);
                        // Smooth scroll to top of table
                        $('html, body').animate({
                            scrollTop: $('#productsTable').offset().top - 100
                        }, 300);
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load products'
                        });
                    },
                    complete: function() {
                        paginationContainer.removeClass('pagination-loading');
                    }
                });
            });
        });
    </script>
@endpush
