<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Dashboard Stats
        $stats = [
            'total_orders'         => Order::count(),
            'total_customers'      => Customer::count(),
            'total_products'       => Product::count(),
            'total_subscriptions'  => Subscription::count(),
            'pending_orders'       => Order::where('order_status', 'pending')->count(),
            'today_orders'         => Order::whereDate('created_at', today())->count(),
            'monthly_revenue'      => Order::where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount'),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
        ];

        // Recent Orders
        $recentOrders = Order::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top Selling Products
        $topProducts = Product::select(
            'products.id',
            'products.name',
            'products.price',
            'products.discount_price',
            'products.product_image',
            'products.status',
            'products.best_seller',
            'products.created_at',
            'products.updated_at',
            DB::raw('COUNT(order_items.id) as order_count')
        )
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy(
                'products.id',
                'products.name',
                'products.price',
                'products.discount_price',
                'products.product_image',
                'products.status',
                'products.best_seller',
                'products.created_at',
                'products.updated_at'
            )
            ->orderBy('order_count', 'desc')
            ->limit(5)
            ->get();

        // Monthly Revenue Chart
        $monthlyRevenue = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as revenue')
        )
            ->where('payment_status', 'paid')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topProducts', 'monthlyRevenue'));
    }
}
