@extends('admin.layouts.app')

@section('title', 'User Subscriptions - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">User Subscriptions</h4>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search subscriptions..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-success">
                            <i class="ri-add-line align-middle me-1"></i> Add Subscription
                        </a>
                    </div>
                </div>

                <!-- Subscriptions Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Plan</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $subscription)
                            <tr>
                                <td>{{ $subscription->id }}</td>
                                <td>{{ $subscription->customer->name ?? 'N/A' }}</td>
                                <td>{{ $subscription->plan_name ?? 'N/A' }}</td>
                                <td>{{ $subscription->start_date ? $subscription->start_date->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    @php
                                        $status = 'Active';
                                        $statusClass = 'success';
                                        if ($subscription->end_date && $subscription->end_date < now()) {
                                            $status = 'Expired';
                                            $statusClass = 'danger';
                                        } elseif (!$subscription->is_active) {
                                            $status = 'Inactive';
                                            $statusClass = 'warning';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td>₹{{ number_format($subscription->amount ?? 0, 2) }}</td>
                                <td>{{ $subscription->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="btn btn-sm btn-warning">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-item" 
                                                data-id="{{ $subscription->id }}" 
                                                data-name="subscription #{{ $subscription->id }}"
                                                data-type="subscription"
                                                data-url="{{ route('admin.subscriptions.destroy', $subscription->id) }}">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No subscriptions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($subscriptions->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $subscriptions->firstItem() }} to {{ $subscriptions->lastItem() }} of {{ $subscriptions->total() }} entries
                    </div>
                    <div class="pagination-container">
                        {{ $subscriptions->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 