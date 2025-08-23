@extends('admin.layouts.app')

@section('title', 'Product Details - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Product Details</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active">Product Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Product: {{ $product->name }}</h4>
                        <div class="btn-group">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-sm me-2">
                                <i class="ri-edit-line"></i> Edit
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                                <i class="ri-arrow-left-line"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Product Image -->
                        <div class="col-md-4">
                            <div class="text-center">
                                @if ($product->product_image)
                                    <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->name }}"
                                        class="img-fluid rounded" style="max-height: 300px;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                        style="height: 300px;">
                                        <i class="ri-image-line text-muted" style="font-size: 4rem;"></i>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Product Name</label>
                                    <p class="form-control-plaintext">{{ $product->name }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Status</label>
                                    <p>
                                        @if ($product->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Category</label>
                                    <p class="form-control-plaintext">
                                        @if ($product->category)
                                            <span class="badge bg-primary">{{ $product->category->name }}</span>
                                        @else
                                            <span class="text-muted">No Category</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Subcategory</label>
                                    <p class="form-control-plaintext">
                                        @if ($product->subcategory)
                                            <span class="badge bg-info">{{ $product->subcategory->subcategory_name }}</span>
                                        @else
                                            <span class="text-muted">No Subcategory</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Price</label>
                                    <p class="form-control-plaintext">
                                        <strong class="text-primary">₹{{ number_format($product->price, 2) }}</strong>
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Discounted Price</label>
                                    <p class="form-control-plaintext">
                                        @if ($product->discount_price)
                                            <strong
                                                class="text-success">₹{{ number_format($product->discount_price, 2) }}</strong>
                                        @else
                                            <span class="text-muted">No discount</span>
                                        @endif
                                    </p>
                                </div>



                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Featured Type</label>
                                    <p class="form-control-plaintext">
                                        @if ($product->featured_type)
                                            <span class="badge bg-warning">{{ ucfirst($product->featured_type) }}</span>
                                        @else
                                            <span class="text-muted">Not featured</span>
                                        @endif
                                    </p>
                                </div>



                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Maximum Order</label>
                                    <p class="form-control-plaintext">
                                        @if ($product->max_order)
                                            {{ $product->max_order }}
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold">Types</label>
                                    <p class="form-control-plaintext">
                                        @if ($product->types && count($product->types) > 0)
                                            @foreach ($product->types as $type)
                                                <span class="badge bg-secondary me-1">{{ $type }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No types selected</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold">Description</label>
                                    <p class="form-control-plaintext">
                                        @if ($product->description)
                                            {{ $product->description }}
                                        @else
                                            <span class="text-muted">No description</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Created At</label>
                                    <p class="form-control-plaintext">
                                        {{ $product->created_at->format('M d, Y h:i A') }}
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Last Updated</label>
                                    <p class="form-control-plaintext">
                                        {{ $product->updated_at->format('M d, Y h:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
