@extends('admin.layouts.app')

@section('title', 'Add SubCategory - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Add New SubCategory</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">SubCategories</a></li>
                        <li class="breadcrumb-item active">Add SubCategory</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">SubCategory Information</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.subcategories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-8">
                                <!-- Category Name -->
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Select Category <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" id="category_id"
                                        name="category_id" required>
                                        <option value="">-- Select Category --</option>
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


                                <!-- Active Status -->
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            value="1" {{ old('is_active') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active Status</label>
                                    </div>
                                </div>

                                <!-- SubCategory Name -->
                                <div class="mb-3">
                                    <label for="subcategory_name" class="form-label">Subcategory Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="subcategory_name" id="subcategory_name"
                                        class="form-control @error('subcategory_name') is-invalid @enderror"
                                        value="{{ old('subcategory_name') }}" placeholder="Enter Subcategory Name" required>
                                    @error('subcategory_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                            </div>

                            <!-- Right Column -->
                            <div class="col-md-4">
                                <!-- SubCategory Image -->
                                <div class="mb-3">
                                    <label for="subcategory_image" class="form-label">SubCategory Image <span
                                            class="text-danger">*</span></label>
                                    <input type="file"
                                        class="form-control @error('subcategory_image') is-invalid @enderror"
                                        id="subcategory_image" name="subcategory_image" accept="image/*" required>
                                    @error('subcategory_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Supported formats: JPG, JPEG, PNG, WEBP<br>
                                        Max size: 2MB
                                    </div>
                                </div>

                                <!-- Image Preview -->
                                <div class="mb-3">
                                    <div id="imagePreview" class="d-none">
                                        <img id="previewImg" src="" alt="Preview" class="img-fluid rounded"
                                            style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Submit Button Row -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2 mb-3 me-3">
                                    <a href="{{ route('admin.subcategories.index') }}" class="btn btn-secondary">
                                        <i class="ri-arrow-left-line align-bottom me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line align-bottom me-1"></i> Create Category
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
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
            // Image preview
            $('#subcategory_image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewImg').attr('src', e.target.result);
                        $('#imagePreview').removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#imagePreview').addClass('d-none');
                }
            });

            // Client-side validation
            $('form').on('submit', function() {
                const name = $('#name').val().trim();
                const image = $('#category_image').val();
                return true;
            });
        });
    </script>
@endpush
