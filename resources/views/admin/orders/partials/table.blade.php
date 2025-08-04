<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th width="5%">#</th>
            <th width="12%">Order Code</th>
            <th width="15%">Customer</th>
            <th width="15%">Delivery Address</th>
            <th width="10%">Amount</th>
            <th width="10%">Status</th>
            <th width="10%">Payment</th>
            <th width="10%">Date</th>
            <th width="13%">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <strong>{{ $order->order_code }}</strong>
            </td>
            <td>
                <div>
                    <strong>{{ $order->customer_name }}</strong>
                    <br><small class="text-muted">{{ $order->customer_email }}</small>
                    <br><small class="text-muted">{{ $order->customer_phone }}</small>
                </div>
            </td>
            <td>
                <div>
                    {{ $order->delivery_address }}
                    <br><small class="text-muted">{{ $order->delivery_city }}, {{ $order->delivery_state }}</small>
                    <br><small class="text-muted">Pincode: {{ $order->delivery_pincode }}</small>
                </div>
            </td>
            <td>
                <div class="order-amount">₹{{ number_format($order->total_amount, 2) }}</div>
                @if($order->delivery_charge > 0)
                    <small class="text-muted">+ ₹{{ number_format($order->delivery_charge, 2) }} delivery</small>
                @endif
            </td>
            <td>
                @php
                    $statusClass = 'status-' . str_replace(' ', '-', strtolower($order->order_status));
                @endphp
                <span class="status-badge {{ $statusClass }}">
                    {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                </span>
            </td>
            <td>
                @php
                    $paymentClass = 'payment-' . strtolower($order->payment_status);
                @endphp
                <span class="payment-badge {{ $paymentClass }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
                <br><small class="text-muted">{{ ucfirst($order->payment_method) }}</small>
            </td>
            <td>
                <div>
                    {{ $order->created_at->format('d M Y') }}
                    <br><small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                </div>
            </td>
            <td>
                <div class="action-buttons">
                    <a href="{{ route('admin.orders.show', $order) }}" 
                       class="btn btn-info btn-sm" 
                       title="View Details">
                        <i class="ri-eye-line"></i>
                    </a>
                    
                    @if($order->order_status === 'pending')
                        <button type="button" 
                                class="btn btn-success btn-sm update-status" 
                                data-id="{{ $order->id }}" 
                                data-status="pending"
                                title="Confirm Order">
                            <i class="ri-check-line"></i>
                        </button>
                    @elseif($order->order_status === 'confirmed')
                        <button type="button" 
                                class="btn btn-warning btn-sm update-status" 
                                data-id="{{ $order->id }}" 
                                data-status="confirmed"
                                title="Ready to Dispatch">
                            <i class="ri-truck-line"></i>
                        </button>
                    @elseif($order->order_status === 'ready')
                        <button type="button" 
                                class="btn btn-primary btn-sm update-status" 
                                data-id="{{ $order->id }}" 
                                data-status="ready"
                                title="Mark as Dispatched">
                            <i class="ri-send-plane-line"></i>
                        </button>
                    @elseif($order->order_status === 'dispatched')
                        <button type="button" 
                                class="btn btn-success btn-sm update-status" 
                                data-id="{{ $order->id }}" 
                                data-status="dispatched"
                                title="Mark as Delivered">
                            <i class="ri-check-double-line"></i>
                        </button>
                    @endif
                    
                    @if($order->order_status !== 'delivered' && $order->order_status !== 'cancelled')
                        <button type="button" 
                                class="btn btn-danger btn-sm cancel-order" 
                                data-id="{{ $order->id }}" 
                                data-code="{{ $order->order_code }}"
                                title="Cancel Order">
                            <i class="ri-close-line"></i>
                        </button>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center py-4">
                <div class="text-muted">
                    <i class="ri-inbox-line fs-2"></i>
                    <p class="mt-2">No orders found</p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@if($orders->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} entries
    </div>
    <div class="pagination-container">
        {{ $orders->appends(request()->query())->links() }}
    </div>
</div>
@endif 