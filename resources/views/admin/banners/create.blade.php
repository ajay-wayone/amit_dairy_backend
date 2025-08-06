@extends('admin.layouts.app')

@section('title', 'Edit Banner')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4>Edit Banner</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Page Name -->
                    <div class="mb-3">
                        <label for="page_name" class="form-label">Page Name</label>
                        <input type="text" name="page_name" id="page_name"
                            class="form-control @error('page_name') is-invalid @enderror"
                            value="{{ old('page_name', $banner->page_name) }}" required>
                        @error('page_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Banner Image Upload -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Change Image (optional)</label>
                        <input type="file" name="image" id="image"
                            class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Supported formats: JPG, JPEG, PNG, WEBP<br>
                            Max size: 2MB. Recommended size: 1440 x 650px
                        </div>
                    </div>

                    <!-- Current Image Preview -->
                    @if ($banner->image)
                        <div class="mb-3">
                            <label>Current Image:</label><br>
                            <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner Image" class="img-fluid rounded"
                                style="max-height: 200px;">
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Banner</button>
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            // Image preview
            $('#image').on('change', function() {
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
                    $('#previewImg').attr('src', '');
                }
            });
        });
    </script>
@endpush
