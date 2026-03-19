<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderItems.product'])->orderBy('created_at', 'desc');

        // AJAX Search
        if ($request->ajax() && $request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->has('status') && $request->status) {
            $query->where('order_status', $request->status);
        }

        // Payment Filter
        if ($request->has('payment') && $request->payment) {
            $query->where('payment_status', $request->payment);
        }

        $orders = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.orders.partials.table', compact('orders'))->render(),
            ]);
        }

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['user', 'orderItems.product']);
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:pending,confirmed,ready,dispatched,delivered,cancelled',
            'payment_status' => 'required|in:pending,completed,failed',
            'order_notes' => 'nullable|string',
            'delivery_date' => 'nullable|date',
        ]);

        try {
            $data = [
                'order_status' => $request->order_status,
                'payment_status' => $request->payment_status,
                'order_notes' => $request->order_notes,
            ];

            if ($request->delivery_date) {
                $data['delivery_date'] = $request->delivery_date;
            }

            if ($request->order_status === 'delivered') {
                $data['delivered_at'] = now();
            }

            $order->update($data);

            return redirect()->route('admin.orders.index')
                ->with('success', 'Order updated successfully!');
        }
        catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        try {
            // Delete order items first
            $order->orderItems()->delete();
            $order->delete();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Order deleted successfully!');
        }
        catch (\Exception $e) {
            return back()->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:confirmed,ready,dispatched,delivered,cancelled',
        ]);

        try {
            $data = ['order_status' => $request->status];

            if ($request->status === 'delivered') {
                $data['delivered_at'] = now();
            }

            $order->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully!',
                'status' => $request->status,
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status!',
            ], 500);
        }
    }
    public function readyOrders()
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->where('order_status', 'ready')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.orders.ready', compact('orders'));
    }

    public function dispatchedOrders()
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->where('order_status', 'dispatched')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.orders.dispatched', compact('orders'));
    }

    public function deliveredOrders()
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->where('order_status', 'delivered')
            ->orderBy('delivered_at', 'desc')
            ->paginate(10);

        return view('admin.orders.delivered', compact('orders'));
    }

    public function cancelledOrders()
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->where('order_status', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.orders.cancelled', compact('orders'));
    }
}
