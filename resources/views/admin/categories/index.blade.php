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
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
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
                        <div class="search-box">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search categories...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Categories Table -->
                <div class="table-responsive">
                    <div id="categoriesTable">
                        @include('admin.categories.partials.table')
                    </div>
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

    // AJAX Search
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimer);
        const searchTerm = $(this).val();
        
        searchTimer = setTimeout(function() {
            performSearch(searchTerm);
        }, 500);
    });

    function performSearch(searchTerm) {
        $.ajax({
            url: '{{ route("admin.categories.index") }}',
            type: 'GET',
            data: {
                search: searchTerm
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#categoriesTable').html(response.html);
                if (response.pagination) {
                    $('.pagination-container').html(response.pagination);
                }
            },
            error: function() {
                console.log('Search failed');
            }
        });
    }

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
                    // Update button text and class
                    if (response.is_active) {
                        button.removeClass('btn-warning').addClass('btn-success');
                        button.html('<i class="ri-check-line"></i> Active');
                    } else {
                        button.removeClass('btn-success').addClass('btn-warning');
                        button.html('<i class="ri-close-line"></i> Inactive');
                    }

                    // Show success message
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
                    'method': 'POST',
                    'action': `/admin/categories/${categoryId}`
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
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const page = $(this).attr('href').split('page=')[1];
        const searchTerm = $('#searchInput').val();
        
        $.ajax({
            url: '{{ route("admin.categories.index") }}',
            type: 'GET',
            data: {
                page: page,
                search: searchTerm
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#categoriesTable').html(response.html);
                if (response.pagination) {
                    $('.pagination-container').html(response.pagination);
                }
            }
        });
    });
});
</script>
@endpush 