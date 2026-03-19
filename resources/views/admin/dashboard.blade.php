@extends('admin.layouts.app')

@section('title', 'Dashboard - Amit Dairy & Sweets')

@section('content')
    <div class="container-fluid">
        
        {{-- Page Title / Breadcrumb --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 1: Key Performance Indicators (KPIs) - Gradient Cards --}}
        <div class="row">
            {{-- 1. Total Orders Card --}}
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card orders dashboard-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon">
                                <i class="bi bi-cart-check text-white fs-1"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-1 text-white">{{ number_format($stats['total_orders'] ?? 0) }}</h4>
                                <p class="mb-0 text-white-50">Total Orders</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Total Customers Card --}}
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card customers dashboard-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon">
                                <i class="bi bi-people text-white fs-1"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-1 text-white">{{ number_format($stats['total_customers'] ?? 0) }}</h4>
                                <p class="mb-0 text-white-50">Total Customers</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Total Products Card --}}
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card products dashboard-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon">
                                <i class="bi bi-box text-white fs-1"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-1 text-white">{{ number_format($stats['total_products'] ?? 0) }}</h4>
                                <p class="mb-0 text-white-50">Total Products</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Monthly Revenue Card (Note: Controller should use 'subtotal') --}}
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card subscription-revenue dashboard-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon">
                                <i class="bi bi-currency-rupee text-white fs-1"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-1 text-white">₹{{ number_format($stats['monthly_revenue'] ?? 0, 0) }}</h4>
                                <p class="mb-0 text-white-50">Monthly Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Additional Stats / Alert Card --}}
        <div class="row">
            
            {{-- 5. PENDING Orders Card --}}
            <div class="col-xl-2 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded">
                                    <span class="avatar-title bg-warning rounded">
                                        <i class="bi bi-clock text-white fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">{{ number_format($stats['pending_orders'] ?? 0) }}</h5>
                                <p class="text-muted mb-0">Pending Orders</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. TODAY's Orders Card --}}
            <div class="col-xl-2 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded">
                                    <span class="avatar-title bg-success rounded">
                                        <i class="bi bi-calendar-day text-white fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">{{ number_format($stats['today_orders'] ?? 0) }}</h5>
                                <p class="text-muted mb-0">Today's Orders</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 7. ACTIVE Subscriptions Card --}}
            <div class="col-xl-2 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded">
                                    <span class="avatar-title bg-info rounded">
                                        <i class="bi bi-star text-white fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">{{ number_format($stats['active_subscriptions'] ?? 0) }}</h5>
                                <p class="text-muted mb-0">Active Subs</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 8. TOTAL Subscriptions Card --}}
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded">
                                    <span class="avatar-title bg-primary rounded">
                                        <i class="bi bi-graph-up text-white fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">{{ number_format($stats['total_subscriptions'] ?? 0) }}</h5>
                                <p class="text-muted mb-0">Total Subscriptions</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- 9. 2 Days Remaining Orders Card (Alert Card) --}}
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card stat-card" id="twoDayOrdersCard" 
                    style="background: linear-gradient(135deg, #ff5858 0%, #f857a6 100%); color: white; cursor: pointer;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">2 Days Remaining Orders</h6>
                                <h3 class="text-white mb-2">{{ number_format($stats['two_day_orders'] ?? 0) }}</h3>
                                <small class="text-white-75">Delivery today or tomorrow</small>
                            </div>
                            <div class="stat-icon" style="background: rgba(255,0,0,0.2);">
                                <i class="bx bx-alarm-exclamation fs-2" style="color:#fff"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: Recent Orders Table & Charts --}}
        <div class="row">
            {{-- Recent Orders --}}
            <div class="col-xl-8">
                <div class="card dashboard-card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Recent Orders</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th>Order Code</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                                    class="text-body fw-bold">
                                                    {{ $order->order_code }}
                                                </a>
                                            </td>
                                            <td>{{ $order->customer_name }}</td>
                                            
                                            {{-- FIX: 'total_amount' को 'subtotal' से बदला गया है --}}
                                            <td>₹{{ number_format($order->subtotal ?? $order->total_amount ?? 0, 2) }}</td>
                                            
                                            <td>
                                                <span
                                                    class="badge bg-{{ $order->order_status === 'pending' ? 'warning' : ($order->order_status === 'delivered' ? 'success' : 'info') }}">
                                                    {{ ucfirst($order->order_status) }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No recent orders found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts and Top Products --}}
            <div class="col-xl-4">
                {{-- Order Status Chart --}}
                <div class="card dashboard-card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Order Status Distribution</h4>
                    </div>
                    <div class="card-body">
                        <div id="orderStatusChart" style="height: 300px;"></div>
                    </div>
                </div>

                {{-- Top Products --}}
                <div class="card dashboard-card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Top Selling Products</h4>
                    </div>
                    <div class="card-body">
                        @forelse($topProducts as $product)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('storage/' . $product->product_image) }}"
                                        alt="{{ $product->name }}" class="rounded" width="40">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $product->name }}</h6>
                                    <p class="text-muted mb-0">{{ number_format($product->order_count ?? 0) }} orders sold</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary">₹{{ number_format($product->price, 0) }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">No products found</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Modal for 2 Days Remaining Orders (Kept for design consistency) --}}
    <div class="modal fade" id="twoDayOrdersModal" tabindex="-1" aria-labelledby="twoDayOrdersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="twoDayOrdersModalLabel">2 Days Remaining Orders (Today & Tomorrow)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center text-muted">Detailed order list will load here (requires separate route/API call).</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* CSS is correct and left unchanged */
        .dashboard-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            border: none;
        }

        .stat-card.orders {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-card.customers {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .stat-card.products {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .stat-card.subscription-revenue {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        }
        
        #twoDayOrdersCard {
            background: linear-gradient(135deg, #ff5858 0%, #f857a6 100%) !important;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            clip-path: polygon(0 0, 100% 0, 100% 30%, 0 70%);
        }

        .stat-icon {
            position: relative;
            z-index: 2;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ECharts Initialization for Order Status
            var chartDom = document.getElementById('orderStatusChart');
            if (chartDom) {
                var myChart = echarts.init(chartDom);
                
                // Chart options: uses the detailed stats passed from the controller
                var option = {
                    tooltip: {
                        trigger: 'item',
                        formatter: '{a} <br/>{b}: {c} ({d}%)'
                    },
                    legend: {
                        orient: 'horizontal',
                        bottom: 0,
                        data: ['Pending', 'Processing', 'Delivered', 'Cancelled']
                    },
                    series: [
                        {
                            name: 'Order Status',
                            type: 'pie',
                            radius: ['50%', '70%'],
                            avoidLabelOverlap: false,
                            itemStyle: {
                                borderRadius: 10,
                                borderColor: '#fff',
                                borderWidth: 2
                            },
                            label: {
                                show: false,
                                position: 'center'
                            },
                            emphasis: {
                                label: {
                                    show: true,
                                    fontSize: '18',
                                    fontWeight: 'bold'
                                }
                            },
                            labelLine: {
                                show: false
                            },
                            data: [
                                { value: {{ $stats['pending_orders'] ?? 0 }}, name: 'Pending' },
                                { value: {{ $stats['processing_orders'] ?? 0 }}, name: 'Processing' },
                                { value: {{ $stats['delivered_orders'] ?? 0 }}, name: 'Delivered' },
                                { value: {{ $stats['cancelled_orders'] ?? 0 }}, name: 'Cancelled' }
                            ]
                        }
                    ],
                    color: ['#FFC107', '#17A2B8', '#28A745', '#DC3545']
                };
                
                // Apply the chart options
                myChart.setOption(option);
                
                // Responsive chart on window resize
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            // Modal functionality for 2 Days Remaining Orders Card
            const twoDayCard = document.getElementById('twoDayOrdersCard');
            if (twoDayCard) {
                twoDayCard.addEventListener('click', function() {
                    // Assuming Bootstrap JS is included, this will show the modal
                    const modal = new bootstrap.Modal(document.getElementById('twoDayOrdersModal'));
                    modal.show();
                });
            }
        });
    </script>
@endpush