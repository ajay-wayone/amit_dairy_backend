@extends('admin.layouts.app')

@section('title', 'Edit testimonial - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit Testimonial</h4>
                    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-success btn-sm">
                        <i class="ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.testimonials.update', $testimonial->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label small">Full Name *</label>
                                <input type="text" name="customer_name" id="customer_name"
                                    placeholder="Enter the customer name.."
                                    class="form-control form-control-sm @error('customer_name') is-invalid @enderror"
                                    value="{{ old('customer_name', $testimonial->customer_name) }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="is_active" class="form-label small">Status *</label>
                                <select name="is_active" id="is_active"
                                    class="form-select form-select-sm @error('is_active') is-invalid @enderror" required>
                                    <option value="1"
                                        {{ old('is_active', $testimonial->is_active) == '1' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0"
                                        {{ old('is_active', $testimonial->is_active) == '0' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="customer_image" class="form-label small">
                                    Image <span class="text-danger">*</span>
                                </label>

                                <input type="file" name="customer_image" id="customer_image"
                                    class="form-control form-control-sm @error('customer_image') is-invalid @enderror"
                                    onchange="previewImage(event)">
                                @error('customer_image')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror

                                {{-- Show current image --}}
                                @if (isset($testimonial) && $testimonial->customer_image)
                                    <div class="mt-2">
                                        <img id="preview" src="{{ asset('storage/' . $testimonial->customer_image) }}"
                                            alt="Current Image" class="img-thumbnail" width="120">
                                    </div>
                                @else
                                    <img id="preview" class="img-thumbnail mt-2" width="120" style="display: none;">
                                @endif
                            </div>


                            <div class="col-md-6">
                                <label for="rating" class="form-label small">Rating (0.1 - 5.0)*</label>
                                <input type="text" name="rating" id="rating" placeholder="Enter the rating.."
                                    class="form-control form-control-sm @error('rating') is-invalid @enderror"
                                    value="{{ old('rating', $testimonial->rating) }}">
                                @error('rating')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="testimonial" class="form-label small">Comment</label>
                                <textarea name="testimonial" id="testimonial" placeholder="Enter the comment.."
                                    class="form-control form-control-sm @error('testimonial') is-invalid @enderror" rows="2">{{ old('testimonial', $testimonial->testimonial) }}</textarea>
                                @error('testimonial')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">Update testimonial</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
