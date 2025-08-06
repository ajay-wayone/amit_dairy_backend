<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th width="5%">#</th>
            <th width="15%">Image</th>
            <th width="20%">Product Name</th>
            <th width="12%">Category</th>
            <th width="12%">Subcategory</th>
            <th width="8%">Price</th>
            <th width="8%">Discounted Price</th>
            <th width="8%">Quantity</th>
            <th width="8%">Status</th>
            <th width="15%">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                            class="product-image rounded">
                    @else
                        <div class="product-image bg-light d-flex align-items-center justify-content-center rounded">
                            <i class="ri-image-line text-muted"></i>
                        </div>
                    @endif
                </td>
                <td>
                    <strong>{{ $product->name }}</strong>
                    @if ($product->featured_type)
                        <br><span class="badge bg-warning">{{ ucfirst($product->featured_type) }}</span>
                    @endif
                    @if ($product->description)
                        <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                    @endif
                </td>
                <td>
                    @if ($product->category)
                        <span class="badge bg-primary">{{ $product->category->name }}</span>
                    @else
                        <span class="text-muted">No Category</span>
                    @endif
                </td>
                <td>
                    @if ($product->subcategory)
                        <span class="badge bg-info">{{ $product->subcategory->subcategory_name }}</span>
                    @else
                        <span class="text-muted">No Subcategory</span>
                    @endif
                </td>
                <td>
                    <strong>₹{{ number_format($product->price, 2) }}</strong>
                </td>
                <td>
                    @if ($product->discounted_price)
                        <strong class="text-success">₹{{ number_format($product->discounted_price, 2) }}</strong>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if ($product->quantity)
                        <span class="badge bg-success">{{ $product->quantity }} {{ $product->unit ?? 'units' }}</span>
                    @else
                        <span class="badge bg-warning">Not Set</span>
                    @endif
                </td>
                <td>
                    <button type="button"
                        class="btn btn-sm toggle-status {{ $product->is_active ? 'btn-success' : 'btn-warning' }}"
                        data-id="{{ $product->id }}">
                        @if ($product->is_active)
                            <i class="ri-check-line"></i> Active
                        @else
                            <i class="ri-close-line"></i> Inactive
                        @endif
                    </button>
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-info"
                            title="View">
                            <i class="ri-eye-line"></i>
                        </a>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning"
                            title="Edit">
                            <i class="ri-edit-line"></i>
                        </a>
                        <button type="button" class="btn btn-danger delete-product" 
                            data-id="{{ $product->id }}" title="Delete">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center py-4">
                    <div class="text-muted">
                        <i class="ri-inbox-line fs-2"></i>
                        <p class="mt-2">No products found</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if($products->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $products->links() }}
    </div>
@endif
