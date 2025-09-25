<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th width="5%">Sr.n</th>
            <th width="12%">Order_Code</th>

            <th width="15%">Customer</th>
            <th width="12%">Email</th>
            <th width="12%">Pincode</th>
            <th width="15%">Delivery_Address</th>
            <th width="10%">Amount</th>
            <th width="10%">Status</th>
            <th width="10%">Payment</th>
            <th width="10%">Delivery_date</th>
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

                <td> {{ $order->customer_name }}</td>
                <td>{{$order->customer_email}}</td>
                <td>{{$order->delivery_pincode}}</td>
                <td>
                    <div>
                        {{ $order->delivery_address }}
                        <br><small class="text-muted">{{ $order->delivery_city }}, {{ $order->delivery_state }}</small>
                    </div>
                </td>
                <td>
                    <div class="order-amount">₹{{ number_format($order->total_amount, 2) }}</div>
                    @if ($order->delivery_charge > 0)
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
                    {{ $order->delivered_at ? $order->delivered_at->format('d M Y') : '' }}

                </td>
                <td>
                    <div class="action-buttons">

                        @if ($order->order_status === 'pending')
                            <button type="button" class="btn btn-success btn-sm update-status" data-id="{{ $order->id }}"
                                data-status="pending" title="Confirm Order">
                                <i class="ri-check-line"></i>
                            </button>
                        @elseif($order->order_status === 'confirmed')
                            <button type="button" class="btn btn-warning btn-sm update-status" data-id="{{ $order->id }}"
                                data-status="confirmed" title="Ready to Dispatch">
                                <i class="ri-truck-line"></i>
                            </button>
                        @elseif($order->order_status === 'ready')
                            <button type="button" class="btn btn-primary btn-sm update-status" data-id="{{ $order->id }}"
                                data-status="ready" title="Mark as Dispatched">
                                <i class="ri-send-plane-line"></i>
                            </button>
                        @elseif($order->order_status === 'dispatched')
                            <button type="button" class="btn btn-success btn-sm update-status" data-id="{{ $order->id }}"
                                data-status="dispatched" title="Mark as Delivered">
                                <i class="ri-check-double-line"></i>
                            </button>
                        @endif

                        @if ($order->order_status !== 'delivered' && $order->order_status !== 'cancelled')
                            <button type="button" class="btn btn-danger btn-sm cancel-order" data-id="{{ $order->id }}"
                                data-code="{{ $order->order_code }}" title="Cancel Order">
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

@if ($orders->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} entries
        </div>
        <div class="pagination-container">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>
@endif