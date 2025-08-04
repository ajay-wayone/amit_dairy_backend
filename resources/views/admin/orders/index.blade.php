@extends('admin.layouts.app')

@section('title', 'Orders - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Orders</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Orders</li>
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
                        <h4 class="card-title mb-0">Manage Orders</h4>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.orders.new') }}" class="btn btn-warning">
                                <i class="ri-time-line align-bottom me-1"></i> New Orders
                            </a>
                            <a href="{{ route('admin.orders.ready') }}" class="btn btn-info">
                                <i class="ri-truck-line align-bottom me-1"></i> Ready to Dispatch
                            </a>
                            <a href="{{ route('admin.orders.delivered') }}" class="btn btn-success">
                                <i class="ri-check-double-line align-bottom me-1"></i> Delivered
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
                            <input type="text" class="form-control" id="searchInput" placeholder="Search orders...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="ready">Ready to Dispatch</option>
                            <option value="dispatched">Dispatched</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="paymentFilter">
                            <option value="">All Payments</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-secondary w-100" id="clearFilters">
                            <i class="ri-refresh-line"></i> Clear
                        </button>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="table-responsive">
                    <div id="ordersTable">
                        @include('admin.orders.partials.table')
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

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-confirmed {
    background-color: #d1ecf1;
    color: #0c5460;
}

.status-ready {
    background-color: #d4edda;
    color: #155724;
}

.status-dispatched {
    background-color: #cce5ff;
    color: #004085;
}

.status-delivered {
    background-color: #d1e7dd;
    color: #0f5132;
}

.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
}

.payment-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.payment-pending {
    background-color: #fff3cd;
    color: #856404;
}

.payment-completed {
    background-color: #d1e7dd;
    color: #0f5132;
}

.payment-failed {
    background-color: #f8d7da;
    color: #721c24;
}

.action-buttons .btn {
    padding: 4px 8px;
    font-size: 12px;
    margin-right: 4px;
}

.order-amount {
    font-weight: 600;
    color: #28a745;
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
        const status = $('#statusFilter').val();
        const payment = $('#paymentFilter').val();
        
        $.ajax({
            url: '{{ route("admin.orders.index") }}',
            type: 'GET',
            data: {
                search: searchTerm,
                status: status,
                payment: payment
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#ordersTable').html(response.html);
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

    $('#statusFilter, #paymentFilter').on('change', function() {
        performSearch();
    });

    $('#clearFilters').on('click', function() {
        $('#searchInput').val('');
        $('#statusFilter').val('');
        $('#paymentFilter').val('');
        performSearch();
    });

    // Update Order Status
    $(document).on('click', '.update-status', function() {
        const orderId = $(this).data('id');
        const currentStatus = $(this).data('status');
        
        const statusOptions = {
            'pending': 'Confirmed',
            'confirmed': 'Ready to Dispatch',
            'ready': 'Dispatched',
            'dispatched': 'Delivered'
        };

        const nextStatus = statusOptions[currentStatus];
        
        if (nextStatus) {
            Swal.fire({
                title: 'Update Order Status',
                text: `Change status to "${nextStatus}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/orders/${orderId}/update-status`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: nextStatus.toLowerCase().replace(/\s+/g, '_')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to update order status'
                            });
                        }
                    });
                }
            });
        }
    });

    // Pagination
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const page = $(this).attr('href').split('page=')[1];
        const searchTerm = $('#searchInput').val();
        const status = $('#statusFilter').val();
        const payment = $('#paymentFilter').val();
        
        $.ajax({
            url: '{{ route("admin.orders.index") }}',
            type: 'GET',
            data: {
                page: page,
                search: searchTerm,
                status: status,
                payment: payment
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#ordersTable').html(response.html);
                if (response.pagination) {
                    $('.pagination-container').html(response.pagination);
                }
            }
        });
    });
});
</script>
@endpush 