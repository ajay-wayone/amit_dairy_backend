@extends('admin.layouts.app')

@section('title', 'Add Banner - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-16 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header  text-white">
                <h4 class="card-title mb-0">Add Banner for {{ ucfirst($page_name) }} Page</h4>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.website-banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="page_name" value="{{ $page_name }}">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="title" class="form-label small fw-bold">Title</label>
                            <input type="text" name="title" id="title" placeholder="Enter banner title"
                                class="form-control form-control-sm @error('title') is-invalid @enderror"
                                value="{{ old('title') }}">
                            @error('title')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="subtitle" class="form-label small fw-bold">Subtitle</label>
                            <input type="text" name="subtitle" id="subtitle" placeholder="Enter banner subtitle"
                                class="form-control form-control-sm @error('subtitle') is-invalid @enderror"
                                value="{{ old('subtitle') }}">
                            @error('subtitle')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="image" class="form-label small fw-bold">Upload Banner <span class="text-danger">*</span></label>
                            <input type="file" name="image" id="image"
                                class="form-control form-control-sm @error('image') is-invalid @enderror" required>
                            <small class="text-muted">Recommended size: 1920x600 px</small>
                            @error('image')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('admin.website-banners.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm">Save Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>.my-large-input {
    height: 50px;        
    font-size: 16px;      
    padding: 10px;        
}

.my-large-input textarea {
    height: 120px;        
    font-size: 16px;      
    padding: 10px;
}
</style>
@endsection
