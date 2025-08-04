<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th width="5%">#</th>
            <th width="12%">Image</th>
            <th width="20%">Name</th>
            <th width="15%">Category</th>
            <th width="10%">Price</th>
            <th width="8%">Stock</th>
            <th width="8%">SKU</th>
            <th width="8%">Status</th>
            <th width="8%">Featured</th>
            <th width="16%">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                @else
                    <div class="product-image bg-light d-flex align-items-center justify-content-center">
                        <i class="ri-image-line text-muted"></i>
                    </div>
                @endif
            </td>
            <td>
                <strong>{{ $product->name }}</strong>
                @if($product->is_featured)
                    <br><span class="badge featured-badge">Featured</span>
                @endif
                @if($product->short_description)
                    <br><small class="text-muted">{{ Str::limit($product->short_description, 50) }}</small>
                @endif
            </td>
            <td>
                @if($product->category)
                    <span class="badge bg-primary">{{ $product->category->name }}</span>
                @else
                    <span class="text-muted">No Category</span>
                @endif
                @if($product->subcategory)
                    <br><small class="text-muted">{{ $product->subcategory->name }}</small>
                @endif
            </td>
            <td>
                <div class="price">₹{{ number_format($product->price, 2) }}</div>
                @if($product->sale_price && $product->sale_price < $product->price)
                    <div class="sale-price">₹{{ number_format($product->sale_price, 2) }}</div>
                @endif
            </td>
            <td>
                @if($product->stock_quantity > 0)
                    <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                @else
                    <span class="badge bg-danger">Out of Stock</span>
                @endif
            </td>
            <td>
                <code>{{ $product->sku }}</code>
            </td>
            <td>
                <button type="button" 
                        class="btn btn-sm toggle-status {{ $product->is_active ? 'btn-success' : 'btn-warning' }}" 
                        data-id="{{ $product->id }}">
                    @if($product->is_active)
                        <i class="ri-check-line"></i> Active
                    @else
                        <i class="ri-close-line"></i> Inactive
                    @endif
                </button>
            </td>
            <td>
                <button type="button" 
                        class="btn btn-sm toggle-featured {{ $product->is_featured ? 'btn-warning' : 'btn-outline-warning' }}" 
                        data-id="{{ $product->id }}">
                    @if($product->is_featured)
                        <i class="ri-star-fill"></i> Featured
                    @else
                        <i class="ri-star-line"></i> Not Featured
                    @endif
                </button>
            </td>
            <td>
                <div class="action-buttons">
                    <a href="{{ route('admin.products.show', $product) }}" 
                       class="btn btn-info btn-sm" 
                       title="View">
                        <i class="ri-eye-line"></i>
                    </a>
                    <a href="{{ route('admin.products.edit', $product) }}" 
                       class="btn btn-warning btn-sm" 
                       title="Edit">
                        <i class="ri-edit-line"></i>
                    </a>
                    <button type="button" 
                            class="btn btn-danger btn-sm delete-product" 
                            data-id="{{ $product->id }}" 
                            data-name="{{ $product->name }}"
                            title="Delete">
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
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line"></i> Add First Product
                    </a>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@if($products->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} entries
    </div>
    <div class="pagination-container">
        {{ $products->appends(request()->query())->links() }}
    </div>
</div>
@endif 