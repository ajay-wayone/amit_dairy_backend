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
                    <form id="filterForm">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="search-box">
                                    <input type="text" class="form-control" id="searchInput" name="search"
                                        placeholder="Search products..." value="{{ request('search') }}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="categoryFilter" name="category_id">
                                    <option value="">All Categories</option>
                                    @foreach ($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="statusFilter" name="status">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-secondary w-100" id="clearFilters">
                                    <i class="ri-refresh-line"></i> Clear
                                </button>
                            </div>
                        </div>
                    </form>

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
        .search-box .form-control {
            padding-right: 40px;
        }
        .search-box .search-icon {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #98a6ad;
            pointer-events: none;
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
                const formData = $('#filterForm').serialize();
                
                $.ajax({
                    url: '{{ route('admin.products.index') }}',
                    type: 'GET',
                    data: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    beforeSend: function() {
                        // Show loading indicator
                        $('#productsTable').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
                    },
                    success: function(response) {
                        $('#productsTable').html(response.html);
                        if (response.pagination) {
                            $('.pagination-container').html(response.pagination);
                        }
                        
                        // Update URL without reloading
                        const params = new URLSearchParams(formData).toString();
                        const newUrl = '{{ route('admin.products.index') }}' + (params ? '?' + params : '');
                        window.history.pushState({}, '', newUrl);
                    },
                    error: function(xhr) {
                        console.log('Search failed');
                        $('#productsTable').html('<div class="alert alert-danger">Failed to load products. Please try again.</div>');
                    }
                });
            }

            // Debounced search input
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(performSearch, 500);
            });

            // Immediate filter changes
            $('#categoryFilter, #statusFilter').on('change', function() {
                performSearch();
            });

            // Clear filters
            $('#clearFilters').on('click', function() {
                $('#filterForm')[0].reset();
                performSearch();
            });

            // Toggle Status
      $(document).on('click', '.toggle-status', function(e) {
    e.preventDefault();
    const button = $(this);
    const productId = button.data('id');
    
    // Show loading state
    const originalHtml = button.html();
    button.html('<i class="ri-loader-4-line spin"></i>');
    button.prop('disabled', true);

    $.ajax({
        url: `/admin/products/${productId}/toggle-status`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            _method: 'POST'
        },
        success: function(response) {
            if (response.success) {
                // Update button appearance
                button.toggleClass('btn-success btn-warning');
                button.html(response.status ? 
                    ' Active' : 
                    'Inactive');
                
                // Show success notification
                Toastify({
                    text: response.message,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#28a745",
                }).showToast();
            } else {
                button.html(originalHtml);
                Toastify({
                    text: response.message || 'Status update failed',
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                }).showToast();
            }
        },
        error: function(xhr) {
            console.error('Status toggle error:', xhr.responseText);
            button.html(originalHtml);
            Toastify({
                text: "Network error. Please try again.",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",
            }).showToast();
        },
        complete: function() {
            button.prop('disabled', false);
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
                                button.removeClass('btn-outline-warning').addClass('btn-warning');
                                button.html('<i class="ri-star-fill"></i> Featured');
                            } else {
                                button.removeClass('btn-warning').addClass('btn-outline-warning');
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

            // Pagination with AJAX
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();

                // Add loading state
                const paginationContainer = $(this).closest('.pagination');
                paginationContainer.addClass('pagination-loading');

                const page = $(this).attr('href').split('page=')[1];
                const formData = $('#filterForm').serialize() + '&page=' + page;

                $.ajax({
                    url: '{{ route('admin.products.index') }}',
                    type: 'GET',
                    data: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        $('#productsTable').html(response.html);
                        // Smooth scroll to top of table
                        $('html, body').animate({
                            scrollTop: $('#productsTable').offset().top - 100
                        }, 300);
                        
                        // Update URL without reloading
                        const params = new URLSearchParams(formData).toString();
                        const newUrl = '{{ route('admin.products.index') }}' + (params ? '?' + params : '');
                        window.history.pushState({}, '', newUrl);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load products. Please try again later.',
                            confirmButtonColor: '#d33'
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