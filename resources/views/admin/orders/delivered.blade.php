@extends('admin.layouts.app')

@section('title', 'Delivered Orders - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Delivered Orders</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">Order ID</th>
                                <th class="text-nowrap">Customer</th>
                                <th class="text-nowrap">Amount</th>
                                <th class="text-nowrap">Status</th>
                                <th class="text-nowrap">Delivered Date</th>
                                <th class="text-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td class="text-nowrap">#{{ $order->order_code }}</td>
                                <td class="text-nowrap">
                                    <div class="small">
                                        <strong>{{ $order->customer_name }}</strong><br>
                                        <span class="text-muted">{{ $order->customer_email }}</span>
                                    </div>
                                </td>
                                <td class="text-nowrap">₹{{ number_format($order->total_amount, 2) }}</td>
                                <td class="text-nowrap">
                                    <span class="badge bg-success badge-sm">Delivered</span>
                                </td>
                                <td class="text-nowrap small">{{ $order->delivered_at ? $order->delivered_at->format('M d, Y H:i') : 'N/A' }}</td>
                                <td class="text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">View</a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">No delivered orders found</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($orders->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="small text-muted">
                        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
                    </div>
                    <div>
                        {{ $orders->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 