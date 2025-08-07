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
            <th width="8%">Status</th>
            <th width="15%">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if ($product->product_image)
                        <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->name }}"
                            class="product-image rounded">
                    @else
                        <div class="product-image bg-light d-flex align-items-center justify-content-center rounded">
                            <i class="ri-image-line text-muted"></i>
                        </div>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.products.show', $product) }}" class="text-decoration-none text-info"
                        title="View">
                        <strong>{{ $product->name }}</strong>
                    </a>

                    @if ($product->featured_type)
                        <br><span class="badge bg-warning">{{ ucfirst($product->featured_type) }}</span>
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
                    @if ($product->discount_price)
                        <strong class="text-success">₹{{ number_format($product->discount_price, 2) }}</strong>
                    @else
                        <span class="text-muted">-</span>
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

                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning me-2"
                            title="Edit">
                            <i class="ri-edit-line"></i>
                        </a>
                        <button type="button" class="btn btn-danger delete-product" data-id="{{ $product->id }}"
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
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if ($products->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="pagination-info mb-2 mb-md-0">
                    Showing <strong>{{ $products->firstItem() }}</strong> to
                    <strong>{{ $products->lastItem() }}</strong> of <strong>{{ $products->total() }}</strong> entries
                </div>
                <nav aria-label="Products pagination">
                    <ul class="pagination mb-0">
                        {{-- Previous Page Link --}}
                        @if ($products->onFirstPage())
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link"><i class="ri-arrow-left-s-line"></i></span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $products->previousPageUrl() }}" rel="prev"
                                    aria-label="Previous">
                                    <i class="ri-arrow-left-s-line"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                            @if ($page == $products->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($products->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $products->nextPageUrl() }}" rel="next"
                                    aria-label="Next">
                                    <i class="ri-arrow-right-s-line"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link"><i class="ri-arrow-right-s-line"></i></span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@else
    <div class="row mt-4">
        <div class="col-12">
            <div class="pagination-info">
                Showing <strong>{{ $products->count() }}</strong> entries
            </div>
        </div>
    </div>
@endif
