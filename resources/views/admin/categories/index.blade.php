@extends('admin.layouts.app')

@section('title', 'Categories - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Categories</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Categories</li>
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
                            <h4 class="card-title mb-0">Manage Categories</h4>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                                    <i class="ri-add-line align-bottom me-1"></i> Add Category
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="position-relative">
                                <form method="GET" action="{{ route('admin.categories.index') }}" class="d-flex">
                                    <input type="text" name="search" class="form-control me-2"
                                        placeholder="Search categories..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>



                <!-- Categories Table -->
                <div class="table-responsive">
                    <div id="categoriesTable">
                        @include('admin.categories.partials.table')
                    </div>
                </div>
                <div class="pagination-container mt-3">
                    @include('admin.categories.partials.pagination', ['categories' => $categories])
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('styles')
    <style>
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

        .category-image {
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

        .action-buttons .btn {
            padding: 4px 8px;
            font-size: 12px;
            margin-right: 4px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let searchTimer;

            // Debounced AJAX Search
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimer);
                const searchTerm = $(this).val();

                searchTimer = setTimeout(() => {
                    loadCategories(1, searchTerm); // Always go to page 1 when searching
                }, 500);
            });

            // Function to load categories with pagination and search
            function loadCategories(page = 1, search = '') {
                $('#loadingIndicator').show();

                $.ajax({
                    url: '{{ route('admin.categories.index') }}',
                    type: 'GET',
                    data: {
                        page: page,
                        search: search
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        $('#categoriesTable').html(response.html);
                        $('.pagination-container').html(response.pagination || '');
                        highlightActivePage(page);
                        updateURL(page, search);
                    },
                    complete: function() {
                        $('#loadingIndicator').hide();
                    },
                    error: function() {
                        console.log('Failed to load categories');
                    }
                });
            }

            // Update browser URL
            function updateURL(page, search) {
                const url = new URL(window.location);
                if (search) {
                    url.searchParams.set('search', search);
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.set('page', page);
                window.history.pushState({}, '', url);
            }

            // Highlight the active pagination page
            function highlightActivePage(currentPage) {
                $('.pagination li').removeClass('active');
                $('.pagination a').each(function() {
                    const page = new URL($(this).attr('href'), window.location.origin).searchParams.get(
                        'page');
                    if (parseInt(page) === parseInt(currentPage)) {
                        $(this).closest('li').addClass('active');
                    }
                });
            }

            // Handle pagination link clicks
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const href = $(this).attr('href');
                const url = new URL(href, window.location.origin);
                const page = url.searchParams.get('page') || 1;
                const searchTerm = $('#searchInput').val() || '';

                loadCategories(page, searchTerm);
            });

            // Toggle Status
            $(document).on('click', '.toggle-status', function() {
                const categoryId = $(this).data('id');
                const button = $(this);

                $.ajax({
                    url: `/admin/categories/${categoryId}/toggle-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            button
                                .toggleClass('btn-warning btn-success')
                                .html(response.is_active ?
                                    '<i class="ri-check-line"></i> Active' :
                                    '<i class="ri-close-line"></i> Inactive');

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

            // Delete Category
            $(document).on('click', '.delete-category', function() {
                const categoryId = $(this).data('id');
                const categoryName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete "${categoryName}"? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = $('<form>', {
                            method: 'POST',
                            action: `/admin/categories/${categoryId}`
                        });

                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_token',
                            value: '{{ csrf_token() }}'
                        }));

                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_method',
                            value: 'DELETE'
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });

            // Initial Load
            const initialSearch = new URL(window.location).searchParams.get('search') || '';
            const initialPage = new URL(window.location).searchParams.get('page') || 1;
            if (initialSearch || initialPage > 1) {
                loadCategories(initialPage, initialSearch);
            }
        });
    </script>
@endpush
