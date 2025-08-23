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
                                <input type="text" name="search" class="form-control me-2"
                                    placeholder="Search subscriptions..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </form>
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
                                @forelse($subscriptions as $key=> $subscription)
                                    <tr>
                                        <td>{{ $key+1}}</td>
                                        <td>{{ $subscription->customer->name ?? 'N/A' }}</td>
                                        <td>{{ $subscription->plan_name ?? 'N/A' }}</td>
                                        <td>{{ $subscription->start_date ? $subscription->start_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td>{{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A' }}
                                        </td>
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
                </div>
            </div>
        </div>
    </div>
@endsection
