@extends('admin.layouts.app')

@section('title', 'Edit Banner - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Banner</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Active Status -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                {{ $banner->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <!-- Banner Image -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Banner Image</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                               id="image" name="image" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Current Image Preview -->
                    @if($banner->image)
                        <div class="mb-3" id="imagePreview">
                            <img id="previewImg" src="{{ asset('storage/'.$banner->image) }}" alt="Current Image"
                                 class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    @else
                        <div class="mb-3 d-none" id="imagePreview">
                            <img id="previewImg" src="#" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Banner</button>
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
        }
    });
});
</script>
@endpush
