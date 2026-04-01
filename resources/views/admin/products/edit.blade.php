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
                    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf
                        @method('PUT')
                        <!-- Hidden inputs for cropped images -->
                        <input type="hidden" name="product_image_cropped" id="product_image_cropped">
                        <div id="croppedGalleryInputs"></div>
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
                                    <label for="category_id" class="form-label">Category <span
                                            class="text-danger">*</span></label>
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
                                    <label for="subcategory_id" class="form-label">Subcategory <span
                                            class="text-danger">*</span></label>
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
                                    <label for="name" class="form-label">Product Name <span
                                            class="text-danger">*</span></label>
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



                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('unit') is-invalid @enderror" name="unit"
                                        id="unit">
                                        <option value="">Select Unit</option>
                                        <option value="gram"
                                            {{ old('unit', $product->unit) == 'gram' ? 'selected' : '' }}>Gram</option>
                                        <option value="kilogram"
                                            {{ old('unit', $product->unit) == 'kilogram' ? 'selected' : '' }}>Kilogram
                                        </option>
                                        <option value="unit"
                                            {{ old('unit', $product->unit) == 'unit' ? 'selected' : '' }}>Unit</option>
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
                                    <label for="price" class="form-label">Price (₹) <span
                                            class="text-danger">*</span></label>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" step="0.01" placeholder="Enter the discounte_price..."
                                        class="form-control @error('discount_price') is-invalid @enderror"
                                        name="discount_price" id="discount_price"
                                        value="{{ old('discounte_price', $product->discount_price) }}">
                                    <label for="discount_price" class="form-label">Discounted Price (₹)</label>
                                    @error('discount_price')
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
                                        <option value="hot"
                                            {{ old('featured_type', $product->featured_type) == 'hot' ? 'selected' : '' }}>
                                            Hot</option>
                                        <option value="new_arrival"
                                            {{ old('featured_type', $product->featured_type) == 'new_arrival' ? 'selected' : '' }}>
                                            New Arrival</option>
                                        <option value="featured"
                                            {{ old('featured_type', $product->featured_type) == 'featured' ? 'selected' : '' }}>
                                            Featured</option>
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
                                    <input class="form-check-input" type="checkbox" id="status" name="status"
                                        value="0" {{ old('status', $product->status) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active Status</label>
                                </div>
                            </div>
                                    <!-- best seller -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="best_seller"
                                                name="best_seller" value="1"
                                                {{ old('best_seller',$product->best_seller) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="best_seller">
                                                <i class="fas fa-star text-warning me-1"></i>Best Seller
                                            </label>
                                        </div>
                                    </div>
                            <!-- Product Image -->
                            <div class="col-md-6">
                                <label for="product_image" class="form-label">Product Image</label>
                                <div class="input-group">
                                    <input type="file"
                                        class="form-control @error('product_image') is-invalid @enderror"
                                        id="product_image" name="product_image" accept="image/*">
                                    <button class="btn btn-outline-secondary" type="button"
                                        id="clearImage">Clear</button>
                                    @error('product_image')
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
                                <div id="imagePreview" class="{{ $product->product_image ? '' : 'd-none' }}">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <img id="previewImg"
                                                src="{{ $product->product_image ? asset('storage/' . $product->product_image) : '' }}"
                                                alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                            <p class="mt-2 mb-0 text-muted">
                                                {{ $product->product_image ? 'Current Image' : 'Image Preview' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Gallery Section -->
                            <div class="col-12 mt-4">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="ri-image-2-line me-2"></i>Product Gallery (Sub Images)
                                </h6>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="sub_images" class="form-label">Add New Gallery Images</label>
                                        <input type="file" class="form-control @error('sub_images') is-invalid @enderror" 
                                               id="sub_images" name="sub_images[]" accept="image/*" multiple>
                                        <div class="form-text">
                                            Select multiple images to add. Supported: JPG, JPEG, PNG, WEBP | Max: 2MB per image
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">New Images Preview</label>
                                        <div id="subImagePreviewContainer" class="d-flex flex-wrap gap-2"></div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label d-block">Current Gallery <small class="text-muted">(Click X to remove)</small></label>
                                        <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded" id="existingSubImagesContainer" style="min-height: 140px;">
                                            @if($product->sub_images && count($product->sub_images) > 0)
                                                @foreach($product->sub_images as $index => $path)
                                                    <div class="position-relative gallery-item" id="gallery-item-{{ $index }}">
                                                        <img src="{{ asset('storage/' . $path) }}" 
                                                             class="img-thumbnail" 
                                                             style="width: 120px; height: 120px; object-fit: cover;">
                                                        <button type="button" 
                                                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle p-0 d-flex align-items-center justify-content-center"
                                                                style="width: 26px; height: 26px; line-height: 1; z-index: 10;"
                                                                onclick="markSubImageForDeletion('{{ $path }}', 'gallery-item-{{ $index }}')"
                                                                title="Delete Image">
                                                            <span style="font-size: 20px; font-weight: bold; margin-top: -2px;">&times;</span>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="w-100 d-flex align-items-center justify-content-center">
                                                    <p class="text-muted mb-0"><i class="ri-information-line me-1"></i>No gallery images uploaded yet.</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div id="deleteSubImagesInputs"></div>
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

    <!-- ✂️ Image Cropping Modal -->
    <div class="modal fade" id="cropperModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="cropperModalLabel">
                        <i class="ri-crop-line me-2"></i>Crop Image (1:1 Aspect Ratio)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" id="cancelCrop" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="img-container">
                        <img id="imageToCrop" src="" style="max-width: 100%; display: block;">
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="flex-grow-1">
                        <p class="text-muted small mb-0">
                            <i class="ri-information-line me-1"></i> Drag to move, scroll to zoom.
                        </p>
                    </div>
                    <button type="button" class="btn btn-secondary" id="skipCrop">Skip</button>
                    <button type="button" class="btn btn-primary" id="saveCrop">
                        <i class="ri-check-line me-1"></i>Apply Crop
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <style>
        .img-container {
            max-height: 500px;
            background-color: #f8f9fa;
            overflow: hidden;
        }
        
        .cropper-view-box, .cropper-face {
            border-radius: 4px;
        }
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
    <!-- Cropper.js JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script>
        $(document).ready(function() {
            const categorySelect = $('#category_id');
            const subcategorySelect = $('#subcategory_id');

            // 🔄 Category change => load subcategories
            categorySelect.on('change', function() {
                const categoryId = $(this).val();
                subcategorySelect.empty().append('<option value="">Loading...</option>');

                if (categoryId) {
                    $.ajax({
                        url: "{{ route('admin.products.get-subcategories') }}",
                        type: 'GET',
                        data: { category_id: categoryId },
                        success: function(response) {
                            subcategorySelect.empty().append('<option value="">Select Subcategory</option>'); 
                            if(response.success && response.data.length > 0) {
                                $.each(response.data, function(index, subcategory) {
                                    subcategorySelect.append(
                                        $('<option></option>')
                                        .val(subcategory.id)
                                        .text(subcategory.subcategory_name)
                                    );
                                });
                            } else {
                                subcategorySelect.append('<option value="">No Subcategories Found</option>');
                            }
                        },
                        error: function() {
                            subcategorySelect.empty().append('<option value="">Error loading</option>');
                        }
                    });
                } else {
                    subcategorySelect.empty().append('<option value="">Select Subcategory</option>');
                }
            });

            // ✂️ Cropper.js Integration
            let cropper;
            const cropperModal = new bootstrap.Modal(document.getElementById('cropperModal'));
            const imageToCrop = document.getElementById('imageToCrop');
            let currentCropMode = 'main'; // 'main' or 'gallery'
            let galleryFilesStack = [];
            let currentGalleryIdx = 0;

            function initCropper() {
                if (cropper) cropper.destroy();
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1, // Force square crop
                    viewMode: 2,
                    dragMode: 'move',
                    autoCropArea: 0.9,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            }

            // Handle Main Image Selection
            $('#product_image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    currentCropMode = 'main';
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imageToCrop.src = e.target.result;
                        cropperModal.show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Handle Gallery Images Selection
            $('#sub_images').on('change', function() {
                const files = this.files;
                if (files && files.length > 0) {
                    currentCropMode = 'gallery';
                    galleryFilesStack = Array.from(files);
                    currentGalleryIdx = 0;
                    $('#croppedGalleryInputs').empty();
                    $('#subImagePreviewContainer').empty();
                    startNextGalleryCrop();
                }
            });

            function startNextGalleryCrop() {
                if (galleryFilesStack.length > 0) {
                    const file = galleryFilesStack.shift();
                    currentGalleryIdx++;
                    $('#cropperModalLabel').html(`<i class="ri-crop-line me-2"></i>Crop Gallery Image ${currentGalleryIdx} (1:1)`);
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imageToCrop.src = e.target.result;
                        cropperModal.show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#cropperModalLabel').html(`<i class="ri-crop-line me-2"></i>Crop Image (1:1 Aspect Ratio)`);
                }
            }

            // Save Crop Action
            $('#saveCrop').on('click', function() {
                const canvas = cropper.getCroppedCanvas({
                    width: 800,
                    height: 800,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });
                const croppedData = canvas.toDataURL('image/jpeg', 0.9);

                if (currentCropMode === 'main') {
                    $('#product_image_cropped').val(croppedData);
                    $('#previewImg').attr('src', croppedData);
                    $('#imagePreview').removeClass('d-none');
                    $('#imagePreview p').text('New Cropped Image');
                    cropperModal.hide();
                } else {
                    // Add to hidden inputs
                    $('#croppedGalleryInputs').append(`<input type="hidden" name="sub_images_cropped[]" value="${croppedData}">`);
                    // Display preview
                    const preview = $(`
                        <div class="position-relative">
                            <img src="${croppedData}" class="img-thumbnail shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                    `);
                    $('#subImagePreviewContainer').append(preview);
                    
                    cropperModal.hide();
                    setTimeout(startNextGalleryCrop, 400); // Small delay for smooth transition
                }
            });

            // Skip Crop Action
            $('#skipCrop').on('click', function() {
                if (currentCropMode === 'main') {
                    $('#product_image_cropped').val('');
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewImg').attr('src', e.target.result);
                        $('#imagePreview').removeClass('d-none');
                    };
                    reader.readAsDataURL(document.getElementById('product_image').files[0]);
                    cropperModal.hide();
                } else {
                    cropperModal.hide();
                    setTimeout(startNextGalleryCrop, 400);
                }
            });

            // Cancel/Close Modal
            $('#cancelCrop, #cropperModal .btn-close').on('click', function() {
                cropperModal.hide();
                if (currentCropMode === 'main') {
                    $('#product_image').val('');
                    $('#previewImg').attr('src', '{{ $product->product_image ? asset('storage/' . $product->product_image) : '' }}');
                    $('#imagePreview p').text('{{ $product->product_image ? 'Current Image' : 'Image Preview' }}');
                }
            });

            document.getElementById('cropperModal').addEventListener('shown.bs.modal', initCropper);
            document.getElementById('cropperModal').addEventListener('hidden.bs.modal', function() {
                if (cropper) cropper.destroy();
            });

            // Main Image Preview Logic (original kept for fallback)
            $('#product_image').on('change', function() {
                // Pre-cropper logic handled above
            });

            // Sub-images (Gallery) Preview Logic (original kept for reference)
            $('#sub_images').on('change', function() {
                // Pre-cropper logic handled above
            });

            // ❌ Clear main image preview
            $('#clearImage').on('click', function() {
                $('#product_image').val('');
                $('#product_image_cropped').val('');
                $('#previewImg').attr('src', '{{ $product->product_image ? asset('storage/' . $product->product_image) : '' }}');
                $('#imagePreview p').text('{{ $product->product_image ? 'Current Image' : 'Image Preview' }}');
            });
        });

        // 🗑️ Mark sub-image for deletion
        function markSubImageForDeletion(path, elementId) {
            if (confirm('Are you sure you want to remove this image from the gallery?')) {
                // Remove preview element
                $(`#${elementId}`).fadeOut(300, function() {
                    $(this).remove();
                    if ($('#existingSubImagesContainer .gallery-item').length === 0) {
                        $('#existingSubImagesContainer').html('<div class="w-100 d-flex align-items-center justify-content-center"><p class="text-muted mb-0"><i class="fas fa-info-circle me-1"></i>No gallery images uploaded yet.</p></div>');
                    }
                });
                // Add hidden input for deletion
                $('#deleteSubImagesInputs').append(`<input type="hidden" name="delete_sub_images[]" value="${path}">`);
            }
        }
    </script>
@endpush
