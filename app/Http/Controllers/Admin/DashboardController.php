<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subscription; 
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        
        // --- 1. Dashboard Stats (KPIs) ---
        $stats = [
            'total_orders'          => Order::count(),
            'total_customers'       => User::count(),
            'total_products'        => Product::count(),
            
            // Subscription Stats
            'total_subscriptions'   => Subscription::count(), 
            
            // FIX 2: सक्रिय सब्सक्रिप्शन (Customer Subscriptions) के लिए 'SubscriptionOrder' मॉडल का उपयोग करें
            // और 'status' column को 'is_active' या आपके 'subscription_orders' table के status column के अनुसार बदलें।
            'active_subscriptions'  => Subscription::where('status', 'active')->count(), 
            
            // Order Status Stats
            'pending_orders'        => Order::where('order_status', 'pending')->count(),
            'processing_orders'     => Order::where('order_status', 'processing')->count(),
            'delivered_orders'      => Order::where('order_status', 'delivered')->count(),
            'cancelled_orders'      => Order::where('order_status', 'cancelled')->count(),
            'today_orders'          => Order::whereDate('created_at', $today)->count(),
            
            // Financial Stats
            // FIX 3: 'monthly_revenue' गणना के लिए 'total_amount' को 'subtotal' से बदला गया
            'monthly_revenue'       => Order::where('payment_status', 'paid')
                                        ->whereMonth('created_at', now()->month)
                                        ->sum('subtotal'), // <--- CORRECTION APPLIED HERE
            
            // 2 Days Remaining Orders
            'two_day_orders'        => Order::whereBetween('delivery_date', [$today, $tomorrow])
                                        ->count(),
        ];

        // --- 2. Recent Orders ---
        $recentOrders = Order::with('user') 
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                // Ensure customer_name exists, falling back to a guest or user name
                $order->customer_name = $order->user->fullname ?? $order->user->name ?? 'Guest User'; 
                return $order;
            });

        // --- 3. Top Selling Products (Logic is correct) ---
        $topProducts = Product::select(
            'products.id',
            'products.name',
            'products.price',
            'products.product_image',
            DB::raw('SUM(order_items.quantity) as order_count') // Sum quantity for better sales metric
        )
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id', 'products.name', 'products.price', 'products.product_image') 
            ->orderBy('order_count', 'desc')
            ->limit(5)
            ->get();
        
        // --- 4. Monthly Revenue Chart Data ---
        // FIX 4: चार्ट के राजस्व के लिए भी 'total_amount' को 'subtotal' से बदला गया
        $monthlyRevenue = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(subtotal) as revenue') // <--- CORRECTION APPLIED HERE
        )
            ->where('payment_status', 'paid')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topProducts', 'monthlyRevenue'));
    }
}