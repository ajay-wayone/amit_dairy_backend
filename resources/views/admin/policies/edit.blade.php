@extends('admin.layouts.app')

@section('title', 'Edit Policy')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Edit Policy</h4>
                    <div class="page-title-right">
                        <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to Policies
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.policies.update', $policy->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Policy Type</label>
                                        <input type="text" class="form-control" id="type" name="type" 
                                               value="{{ $policy->type_label }}" readonly>
                                        <small class="text-muted">Policy type cannot be changed</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title', $policy->title) }}" 
                                               placeholder="Enter policy title" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="meta_title" class="form-label">Meta Title</label>
                                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                               id="meta_title" name="meta_title" 
                                               value="{{ old('meta_title', $policy->meta_title) }}" 
                                               placeholder="Enter meta title">
                                        @error('meta_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label">Meta Description</label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                                  id="meta_description" name="meta_description" rows="3" 
                                                  placeholder="Enter meta description">{{ old('meta_description', $policy->meta_description) }}</textarea>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="content" name="content" rows="10" 
                                          placeholder="Enter policy content" required>{{ old('content', $policy->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           value="1" {{ old('is_active', $policy->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Update Policy
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#content'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush 