@extends('admin.layouts.app')

@section('title', 'Edit Banner - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-16 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header text-white">
                <h4 class="card-title mb-0">Edit Banner for {{ ucfirst($banner->page_name) }} Page</h4>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.website-banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="page_name" value="{{ $banner->page_name }}">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="title" class="form-label small fw-bold">Title</label>
                            <input type="text" name="title" id="title"
                                class="form-control form-control-sm @error('title') is-invalid @enderror"
                                value="{{ old('title', $banner->title) }}">
                            @error('title')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="subtitle" class="form-label small fw-bold">Subtitle</label>
                            <input type="text" name="subtitle" id="subtitle"
                                class="form-control form-control-sm @error('subtitle') is-invalid @enderror"
                                value="{{ old('subtitle', $banner->subtitle) }}">
                            @error('subtitle')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="image" class="form-label small fw-bold">Upload Banner</label>
                            <input type="file" name="image" id="image"
                                class="form-control form-control-sm @error('image') is-invalid @enderror">
                            <small class="text-muted">Recommended size: 1920x600 px</small>
                            @if($banner->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/'.$banner->image) }}" width="150" alt="Current Banner">
                                </div>
                            @endif
                            @error('image')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('admin.website-banners.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm">Update Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.my-large-input {
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
