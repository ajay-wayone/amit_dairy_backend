@extends('admin.layouts.app')

@section('title', 'Customers - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Customers</h4>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search customers..."
                                autocomplete="off">
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.customers.create') }}" class="btn btn-success btn-sm">
                                <i class="ri-add-line me-1"></i> Add Customer
                            </a>
                        </div>
                    </div>

                    <!-- Customers Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-nowrap">ID</th>
                                    <th class="text-nowrap">Name</th>
                                    
                                    <th class="text-nowrap">Email</th>
                                    <th class="text-nowrap">Phone</th>
                                    <th class="text-nowrap">Status</th>
                                    <th class="text-nowrap">Joining Date</th>
                                    <th class="text-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="customersTableBody">
                                @include('admin.customers.partials.table')
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @include('admin.customers.partials.pagination')
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let searchTimeout;

                $('#searchInput').on('input', function() {
                    const query = $(this).val();
                    clearTimeout(searchTimeout);

                    // If search is empty, show all customers
                    if (query.length === 0) {
                        loadCustomers('');
                        return;
                    }

                    // If search is too short, don't search yet
                    if (query.length < 2) {
                        return;
                    }

                    searchTimeout = setTimeout(function() {
                        loadCustomers(query);
                    }, 300);
                });

                function loadCustomers(query) {
                    $.ajax({
                        url: '{{ route('admin.customers.index') }}',
                        method: 'GET',
                        data: {
                            search: query
                        },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            if (response.table) {
                                // AJAX response with partial views
                                $('#customersTableBody').html(response.table);
                                $('.pagination-container').html(response.pagination || '');
                            } else {
                                // Full page response
                                const tempDiv = $('<div>').html(response);
                                const newTableBody = tempDiv.find('#customersTableBody').html();
                                const newPagination = tempDiv.find('.pagination-container').html();

                                $('#customersTableBody').html(newTableBody);
                                $('.pagination-container').html(newPagination || '');
                            }

                            // Update URL without page reload
                            const url = new URL(window.location);
                            if (query) {
                                url.searchParams.set('search', query);
                            } else {
                                url.searchParams.delete('search');
                            }
                            url.searchParams.delete('page'); // Reset to first page
                            window.history.pushState({}, '', url);
                        },
                        error: function() {
                            console.log('Search failed');
                        }
                    });
                }

                // Handle pagination clicks without page reload
                $(document).on('click', '.pagination a', function(e) {
                    e.preventDefault();
                    const href = $(this).attr('href');
                    const url = new URL(href);
                    const searchQuery = url.searchParams.get('search') || '';
                    const page = url.searchParams.get('page') || 1;

                    $.ajax({
                        url: '{{ route('admin.customers.index') }}',
                        method: 'GET',
                        data: {
                            search: searchQuery,
                            page: page
                        },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            if (response.table) {
                                // AJAX response with partial views
                                $('#customersTableBody').html(response.table);
                                $('.pagination-container').html(response.pagination || '');
                            } else {
                                // Full page response
                                const tempDiv = $('<div>').html(response);
                                const newTableBody = tempDiv.find('#customersTableBody').html();
                                const newPagination = tempDiv.find('.pagination-container').html();

                                $('#customersTableBody').html(newTableBody);
                                $('.pagination-container').html(newPagination || '');
                            }

                            // Update URL
                            window.history.pushState({}, '', href);

                            // Scroll to top of table
                            $('html, body').animate({
                                scrollTop: $('.table-responsive').offset().top - 100
                            }, 300);
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
