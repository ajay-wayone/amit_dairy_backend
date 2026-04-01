@extends('admin.layouts.app')

@section('title', 'Add Product - Admin Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Add New Product</h4>
                    <div class="page-title-right">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                                <li class="breadcrumb-item active">Add Product</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="row justify-content-center">
            <div class="col-lg-16">
                <div class="card shadow-sm">


                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
                        id="productForm">
                        @csrf
                        <!-- Hidden inputs for cropped images -->
                        <input type="hidden" name="product_image_cropped" id="product_image_cropped">
                        <div id="croppedGalleryInputs"></div>
                        <div class="card-body">
                            <!-- Basic Information Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="ri-information-line me-2"></i>Basic Information
                                    </h6>
                                </div>

                                <!-- Category & Subcategory -->
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label fw-bold">
                                        Category <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('category_id') is-invalid @enderror"
                                        name="category_id" id="category_id" required>
                                        <option value="">Choose Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="subcategory_id" class="form-label fw-bold">
                                        Subcategory <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('subcategory_id') is-invalid @enderror"
                                        name="subcategory_id" id="subcategory_id" required>
                                        <option value="">Choose Subcategory</option>
                                    </select>
                                    @error('subcategory_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Product Name -->
                                <div class="col-md-12 mb-3">
                                    <label for="name" class="form-label fw-bold">
                                        Product Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Enter product name"
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label fw-bold">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description"
                                        rows="4" placeholder="Enter product description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Product Details Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="ri-settings-line me-2"></i>Product Details
                                    </h6>
                                </div>

                                <!-- Weight & Weight Type -->
                                <div class="col-md-6 mb-3">
                                    <label for="weight" class="form-label fw-bold">Weight</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('weight') is-invalid @enderror" name="weight"
                                        id="weight" placeholder="Enter weight..." value="{{ old('weight') }}">
                                    @error('weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="weight_type" class="form-label fw-bold">Weight Type</label>
                                    <select class="form-select @error('weight_type') is-invalid @enderror"
                                        name="weight_type" id="weight_type">
                                        <option value="">Select Weight Type</option>
                                        <option value="gram" {{ old('weight_type') == 'gram' ? 'selected' : '' }}>Gram
                                        </option>
                                        <option value="kilogram" {{ old('weight_type') == 'kilogram' ? 'selected' : '' }}>
                                            Kilogram</option>
                                        <option value="pound" {{ old('weight_type') == 'pound' ? 'selected' : '' }}>Pound
                                        </option>
                                    </select>
                                    @error('weight_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Product Types -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Product Types</label>
                                    <div class="row g-2">
                                        @php $selectedTypes = old('type', []); @endphp
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="type[]"
                                                    value="500" id="type500"
                                                    {{ in_array('500', $selectedTypes) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="type500">500</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="type[]"
                                                    value="750" id="type750"
                                                    {{ in_array('750', $selectedTypes) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="type750">750</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="type[]"
                                                    value="1000" id="type1000"
                                                    {{ in_array('1000', $selectedTypes) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="type1000">1000</label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tags -->
                                <!-- Featured Type -->
                                <div class="col-md-6">
                                    <label for="featured_type" class="form-label fw-bold">Features Type </label>

                                    <select class="form-select @error('featured_type') is-invalid @enderror"
                                        name="featured_type" id="featured_type">
                                        <option value="">Select Featured Type</option>
                                        <option value="hot" {{ old('featured_type') == 'hot' ? 'selected' : '' }}>
                                            Hot</option>
                                        <option value="new_arrival"
                                            {{ old('featured_type') == 'new_arrival' ? 'selected' : '' }}>
                                            New Arrival</option>
                                        <option value="featured"
                                            {{ old('featured_type') == 'featured' ? 'selected' : '' }}>
                                            Featured</option>
                                    </select>
                                    @error('featured_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Pricing Section -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary border-bottom pb-2 mb-3">
                                            <i class="ri-price-tag-3-line me-2"></i>Pricing Information
                                        </h6>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="price" class="form-label fw-bold">
                                            Price (₹) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01"
                                                class="form-control @error('price') is-invalid @enderror" name="price"
                                                id="price" placeholder="0.00" value="{{ old('price') }}" required>
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="discount_price" class="form-label fw-bold">Discount Price (₹)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01"
                                                class="form-control @error('discount_price') is-invalid @enderror"
                                                name="discount_price" id="discount_price" placeholder="0.00"
                                                value="{{ old('discount_price') }}">
                                        </div>
                                        @error('discount_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="min_order" class="form-label fw-bold">Minimum Order</label>
                                        <input type="number"
                                            class="form-control @error('min_order') is-invalid @enderror" name="min_order"
                                            id="min_order" placeholder="1" value="{{ old('min_order', 1) }}">
                                        @error('min_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="max_order" class="form-label fw-bold">Maximum Order</label>
                                        <input type="number"
                                            class="form-control @error('max_order') is-invalid @enderror"
                                            name="max_order" id="max_order" placeholder="Enter maximum order"
                                            value="{{ old('max_order') }}">
                                        @error('max_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Product Status Section -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary border-bottom pb-2 mb-3">
                                            <i class="ri-toggle-line me-2"></i>Product Status
                                        </h6>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="best_seller"
                                                name="best_seller" value="1"
                                                {{ old('best_seller') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="best_seller">
                                                <i class="ri-star-fill text-warning me-1"></i>Best Seller
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="specialities"
                                                name="specialities" value="1"
                                                {{ old('specialities') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="specialities">
                                                <i class="ri-medal-line text-info me-1"></i>Specialities
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="status"
                                                name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="status">
                                                <i class="ri-checkbox-circle-line text-success me-1"></i>Active Status
                                            </label>
                                        </div>
                                    </div>
                                                                </div>

                                <!-- Product Image & Gallery Section -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary border-bottom pb-2 mb-3">
                                            <i class="ri-image-2-line me-2"></i>Product Images
                                        </h6>
                                    </div>

                                    <!-- Main Image -->
                                    <div class="col-md-6 mb-4">
                                        <label for="product_image" class="form-label fw-bold">
                                            Main Product Image <span class="text-danger">*</span>
                                        </label>
                                        <input type="file"
                                            class="form-control @error('product_image') is-invalid @enderror"
                                            id="product_image" name="product_image" accept="image/*">
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Supported: JPG, JPEG, PNG, WEBP | Max: 2MB
                                        </div>
                                        @error('product_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        
                                        <div id="imagePreview" class="mt-3 d-none">
                                            <div class="position-relative d-inline-block">
                                                <img id="previewImg" src="" class="img-thumbnail" style="max-height: 200px;">
                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle p-0 d-flex align-items-center justify-content-center" 
                                                        id="clearImage" style="width: 24px; height: 24px; line-height: 1;">
                                                    <span style="font-size: 18px; font-weight: bold; margin-top: -2px;">&times;</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Gallery (Sub Images) -->
                                    <div class="col-md-6 mb-4">
                                        <label for="sub_images" class="form-label fw-bold">
                                            Gallery Images <small class="text-muted">(Optional)</small>
                                        </label>
                                        <input type="file"
                                            class="form-control @error('sub_images') is-invalid @enderror"
                                            id="sub_images" name="sub_images[]" accept="image/*" multiple>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Select multiple thumbnail images.
                                        </div>
                                        
                                        <div id="subImagePreviewContainer" class="d-flex flex-wrap gap-2 mt-3">
                                            <!-- Gallery previews will appear here -->
                                        </div>
                                    </div>
                                </div>  </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="card-footer bg-light">
                                <div class="d-flex justify-content-between align-items-center">

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="fas fa-save me-2"></i>Create Product
                                        </button>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
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
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-check-input:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(199, 200, 203, 0.25);
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            border-bottom: 1px solid rgba(237, 229, 229, 0.13);
        }

        .form-label {
            color: #495057;
            font-weight: 500;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        .text-primary {
            color: #0d6efd !important;
        }

        .border-bottom {
            border-bottom: 2px solid #e9ecef !important;
        }

        #imagePreview {
            transition: all 0.3s ease;
        }

        .form-text {
            color: #6c757d;
            font-size: 0.875rem;
        }
    </style>
@endpush

@push('scripts')
    <!-- Cropper.js JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script>
        $(document).ready(function() {
            console.log('Product create page loaded');

            const categorySelect = $('#category_id');
            const subcategorySelect = $('#subcategory_id');

            // Category change handler
            categorySelect.on('change', function() {
                const categoryId = $(this).val();
                console.log('Category changed to:', categoryId);

                subcategorySelect.empty().append('<option value="">Loading...</option>');

                if (categoryId) {
                    $.ajax({
                        url: "{{ route('admin.products.get-subcategories') }}",
                        type: 'GET',
                        data: {
                            category_id: categoryId
                        },
                        success: function(data) {
                            subcategorySelect.empty().append(
                                '<option value="">Choose Subcategory</option>');

                            if (data.success && data.data && data.data.length > 0) {
                                $.each(data.data, function(index, subcategory) {
                                    subcategorySelect.append(
                                        $('<option></option>')
                                        .val(subcategory.id)
                                        .text(subcategory.subcategory_name)
                                    );
                                });
                                console.log('Loaded', data.count, 'subcategories');
                            } else {
                                console.log('No subcategories found');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', error);
                            subcategorySelect.empty().append(
                                '<option value="">Error loading subcategories</option>');
                        }
                    });
                } else {
                    subcategorySelect.empty().append('<option value="">Choose Subcategory</option>');
                }
            });

            // Trigger category change if editing
            @if (old('category_id'))
                categorySelect.trigger('change');
            @endif

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
                    // If skipped, we don't add a cropped version, and the server will fall back to raw files if needed
                    // (But for consistency, we recommend cropping all)
                    cropperModal.hide();
                    setTimeout(startNextGalleryCrop, 400);
                }
            });

            // Cancel/Close Modal
            $('#cancelCrop, #cropperModal .btn-close').on('click', function() {
                cropperModal.hide();
                // Clear inputs if cancelled to avoid confusion
                if (currentCropMode === 'main') {
                    $('#product_image').val('');
                    $('#imagePreview').addClass('d-none');
                }
            });

            document.getElementById('cropperModal').addEventListener('shown.bs.modal', initCropper);
            document.getElementById('cropperModal').addEventListener('hidden.bs.modal', function() {
                if (cropper) cropper.destroy();
            });

            // Form validation
            $('#productForm').on('submit', function(e) {
                const categoryId = $('#category_id').val();
                const subcategoryId = $('#subcategory_id').val();
                const productName = $('#name').val().trim();
                const price = $('#price').val();
                const image = $('#product_image').val() || $('#product_image_cropped').val();

                let isValid = true;
                let errors = [];

                if (!categoryId) errors.push('Please select a category');
                if (!subcategoryId) errors.push('Please select a subcategory');
                if (!productName) errors.push('Please enter a product name');
                if (!price || price <= 0) errors.push('Please enter a valid price');
                if (!image) errors.push('Please select a product image');

                if (errors.length > 0) {
                    e.preventDefault();
                    alert('Please fix the following errors:\n• ' + errors.join('\n• '));
                    return false;
                }

                console.log('Form submitted successfully');
            });
        });
    </script>
@endpush
