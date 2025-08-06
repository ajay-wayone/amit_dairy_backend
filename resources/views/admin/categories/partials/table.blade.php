<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th width="5%">#</th>
            <th width="15%">Image</th>
            <th width="20%">Category</th>
            <th width="10%">Status</th>
            <th width="10%">Created_at</th>
            <th width="15%">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($categories as $category)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if ($category->category_image)
                        <img src="{{ asset('storage/' . $category->category_image) }}" alt="{{ $category->name }}"
                            class="category-image" width="100">
                    @else
                        <div class="category-image bg-light d-flex align-items-center justify-content-center">
                            <i class="ri-image-line text-muted"></i>
                        </div>
                    @endif
                </td>

                <td>
                    <strong>{{ $category->name }}</strong>
                    @if ($category->sort_order > 0)
                        <br><small class="text-muted">Order: {{ $category->sort_order }}</small>
                    @endif
                </td>
                <td>
                    <button type="button"
                        class="btn btn-sm toggle-status {{ $category->is_active ? 'btn-success' : 'btn-warning' }}"
                        data-id="{{ $category->id }}">
                        @if ($category->is_active)
                            <i class="ri-check-line"></i> Active
                        @else
                            <i class="ri-close-line"></i> Inactive
                        @endif
                    </button>
                </td>
                <td>
                    <span class="btn-info">{{ $category->created_at->format('d-m-Y') }}</span>
                </td>




                <td>
                    <div class="action-buttons">
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning btn-sm"
                            title="Edit">
                            <i class="ri-edit-line"></i>
                        </a>


                        <button type="button" class="btn btn-danger btn-sm delete-category"
                            data-id="{{ $category->id }}" data-name="{{ $category->name }}" title="Delete">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="text-muted">
                        <i class="ri-inbox-line fs-2"></i>
                        <p class="mt-2">No categories found</p>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                            <i class="ri-add-line"></i> Add First Category
                        </a>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
