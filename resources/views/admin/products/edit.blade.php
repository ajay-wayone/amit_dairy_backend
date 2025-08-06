@extends('admin.layouts.app')

@section('title', 'Edit Product - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Product</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active">Edit Product</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Product: {{ $product->name }}</h4>
            </div>
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Category & Subcategory -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select @error('category_id') is-invalid @enderror"
                                    name="category_id" id="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select @error('subcategory_id') is-invalid @enderror"
                                    name="subcategory_id" id="subcategory_id" required>
                                    <option value="">Select Subcategory</option>
                                    @foreach ($subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}"
                                            {{ old('subcategory_id', $product->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                            {{ $subcategory->subcategory_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="subcategory_id" class="form-label">Subcategory <span class="text-danger">*</span></label>
                                @error('subcategory_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Product Name -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Enter product name"
                                    value="{{ old('name', $product->name) }}" required>
                                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea placeholder="Enter the desc..." class="form-control @error('description') is-invalid @enderror"
                                    name="description" id="description" style="height: 100px">{{ old('description', $product->description) }}</textarea>
                                <label for="description" class="form-label">Description</label>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Quantity & Unit -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" placeholder="Enter the quantity..."
                                    class="form-control @error('quantity') is-invalid @enderror" name="quantity"
                                    id="quantity" value="{{ old('quantity', $product->quantity) }}">
                                <label for="quantity" class="form-label">Quantity</label>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select @error('unit') is-invalid @enderror" name="unit" id="unit">
                                    <option value="">Select Unit</option>
                                    <option value="gram" {{ old('unit', $product->unit) == 'gram' ? 'selected' : '' }}>Gram</option>
                                    <option value="kilogram" {{ old('unit', $product->unit) == 'kilogram' ? 'selected' : '' }}>Kilogram</option>
                                    <option value="unit" {{ old('unit', $product->unit) == 'unit' ? 'selected' : '' }}>Unit</option>
                                </select>
                                <label for="unit" class="form-label">Unit</label>
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Types Checkbox Group -->
                        <div class="col-md-6">
                            <label class="form-label">Types</label>
                            @php
                                $selectedTypes = old('type', $product->types ?? []);
                            @endphp
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="type[]" value="500"
                                        id="type500" {{ in_array('500', $selectedTypes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type500">500</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="type[]" value="750"
                                        id="type750" {{ in_array('750', $selectedTypes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type750">750</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="type[]" value="1000"
                                        id="type1000" {{ in_array('1000', $selectedTypes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type1000">1000</label>
                                </div>
                            </div>
                            @error('type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pricing Section -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" placeholder="Enter the price..."
                                    class="form-control @error('price') is-invalid @enderror" name="price"
                                    id="price" value="{{ old('price', $product->price) }}" required>
                                <label for="price" class="form-label">Price (₹) <span class="text-danger">*</span></label>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" placeholder="Enter the discounted_price..."
                                    class="form-control @error('discounted_price') is-invalid @enderror"
                                    name="discounted_price" id="discounted_price"
                                    value="{{ old('discounted_price', $product->discounted_price) }}">
                                <label for="discounted_price" class="form-label">Discounted Price (₹)</label>
                                @error('discounted_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Quantity Controls -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" placeholder="Enter the minimum_quantity...."
                                    class="form-control @error('minimum_quantity') is-invalid @enderror"
                                    name="minimum_quantity" id="minimum_quantity"
                                    value="{{ old('minimum_quantity', $product->minimum_quantity) }}">
                                <label for="minimum_quantity" class="form-label">Minimum Order Quantity</label>
                                @error('minimum_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" placeholder="Enter the Maximum order..."
                                    class="form-control @error('max_order') is-invalid @enderror" name="max_order"
                                    id="max_order" value="{{ old('max_order', $product->max_order) }}">
                                <label for="max_order" class="form-label">Maximum Order</label>
                                @error('max_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Featured Type -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select @error('featured_type') is-invalid @enderror"
                                    name="featured_type" id="featured_type">
                                    <option value="">Select Featured Type</option>
                                    <option value="hot" {{ old('featured_type', $product->featured_type) == 'hot' ? 'selected' : '' }}>Hot</option>
                                    <option value="new_arrival" {{ old('featured_type', $product->featured_type) == 'new_arrival' ? 'selected' : '' }}>New Arrival</option>
                                    <option value="featured" {{ old('featured_type', $product->featured_type) == 'featured' ? 'selected' : '' }}>Featured</option>
                                </select>
                                <label for="featured_type" class="form-label">Featured Type</label>
                                @error('featured_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Active Status -->
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active Status</label>
                            </div>
                        </div>

                        <!-- Product Image -->
                        <div class="col-md-6">
                            <label for="products_image" class="form-label">Product Image</label>
                            <div class="input-group">
                                <input type="file" class="form-control @error('products_image') is-invalid @enderror"
                                    id="products_image" name="products_image" accept="image/*">
                                <button class="btn btn-outline-secondary" type="button" id="clearImage">Clear</button>
                                @error('products_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">
                                Supported formats: JPG, JPEG, PNG, WEBP<br>
                                Max size: 2MB<br>
                                Leave empty to keep current image
                            </div>
                        </div>

                        <!-- Current Image & Preview -->
                        <div class="col-md-6">
                            <div id="imagePreview" class="{{ $product->image ? '' : 'd-none' }}">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <img id="previewImg" src="{{ $product->image ? asset('storage/' . $product->image) : '' }}" 
                                            alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                        <p class="mt-2 mb-0 text-muted">
                                            {{ $product->image ? 'Current Image' : 'Image Preview' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Update Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-check-input:checked {
        background-color: #556ee6;
        border-color: #556ee6;
    }

    #imagePreview {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
    }

    #previewImg {
        max-width: 100%;
        height: auto;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const categorySelect = $('#category_id');
        const subcategorySelect = $('#subcategory_id');

        // 🔄 Category change => load subcategories
        categorySelect.on('change', function() {
            const categoryId = $(this).val();
            const currentSubcategoryId = '{{ $product->subcategory_id }}';
            
            subcategorySelect.empty().append('<option value="">Loading...</option>');

            if (categoryId) {
                $.ajax({
                    url: "{{ route('admin.products.get-subcategories') }}",
                    type: 'GET',
                    data: {
                        category_id: categoryId
                    },
                    success: function(data) {
                        subcategorySelect.empty().append('<option value="">Select Subcategory</option>');
                        $.each(data, function(index, subcategory) {
                            const selected = subcategory.id == currentSubcategoryId ? 'selected' : '';
                            subcategorySelect.append(
                                $('<option></option>').val(subcategory.id).text(
                                    subcategory.subcategory_name).prop('selected', selected)
                            );
                        });
                    },
                    error: function() {
                        subcategorySelect.empty().append('<option value="">Error loading subcategories</option>');
                    }
                });
            } else {
                subcategorySelect.empty().append('<option value="">Select Subcategory</option>');
            }
        });

        // 🖼️ Image Preview
        $('#products_image').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImg').attr('src', e.target.result);
                    $('#imagePreview').removeClass('d-none');
                    $('#imagePreview p').text('New Image Preview');
                };
                reader.readAsDataURL(file);
            }
        });

        // ❌ Clear image preview
        $('#clearImage').on('click', function() {
            $('#products_image').val('');
            $('#previewImg').attr('src', '{{ $product->image ? asset("storage/" . $product->image) : "" }}');
            $('#imagePreview').removeClass('d-none');
            $('#imagePreview p').text('{{ $product->image ? "Current Image" : "Image Preview" }}');
        });
    });
</script>
@endpush