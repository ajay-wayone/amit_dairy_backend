@extends('admin.layouts.app')

@section('title', 'Add Category - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Add New Category</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                        <li class="breadcrumb-item active">Add Category</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Category Information</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Category Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Enter category name"
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            value="1" {{ old('is_active') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active Status</label>
                                    </div>
                                </div>
                                <!-- featured  -->
                               

                                        <div class="col-md-4 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="featured"
                                                name="featured" value="1"
                                                {{ old('featured') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="featured">
                                                <i class="fas fa-gem text-info me-1"></i>Featured 
                                            </label>
                                        </div>
                                    </div>
                                @if (isset($boxes) && $boxes->count() > 0)
                                    <div class="mb-4">
                                        <label class="form-label fw-bold fs-6 text-primary">Associated Boxes</label>

                                        <div class="card p-3 shadow-sm border border-1 rounded-3">
                                            <div class="row g-3">
                                                @foreach ($boxes as $box)
                                                    <div class="col-md-4">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="box_ids_json[]" value="{{ $box->id }}"
                                                                id="box_{{ $box->id }}"
                                                                {{ in_array($box->id, old('box_ids_json', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="box_{{ $box->id }}">
                                                                {{ $box->box_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        @error('box_ids_json')
                                            <div class="text-danger mt-2 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif

                            </div>

                            <!-- Right Column -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="category_image" class="form-label">Category Image <span
                                            class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('category_image') is-invalid @enderror"
                                        id="category_image" name="category_image" accept="image/*" required>
                                    @error('category_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Supported formats: JPG, JPEG, PNG, WEBP<br>
                                        Max size: 2MB
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div id="imagePreview" class="d-none">
                                        <img id="previewImg" src="" alt="Preview" class="img-fluid rounded"
                                            style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2 mb-3 me-3">
                                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
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
            $('#category_image').on('change', function() {
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

                if (!name) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Category name is required!'
                    });
                    return false;
                }

                if (!image) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Category image is required!'
                    });
                    return false;
                }

                return true;
            });
        });
    </script>
@endpush
