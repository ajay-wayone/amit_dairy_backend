@extends('admin.layouts.app')

@section('title', 'Add Banner - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Banner Information</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Active Status -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <!-- Banner Image -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Banner Image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                               id="image" name="image" accept="image/*" required>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image Preview -->
                    <div class="mb-3 d-none" id="imagePreview">
                        <img id="previewImg" src="#" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Banner</button>
                    </div>

                </form>
            </div>
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
            }
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').addClass('d-none');
        }
    });
});
</script>
@endpush
