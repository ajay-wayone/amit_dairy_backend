@extends('admin.layouts.app')

@section('title', 'Add testimonial - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Add New Testimonial</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label small">Full Name *</label>
                                <input type="text" name="customer_name" id="customer_name"
                                    placeholder="Enter the customer name.."
                                    class="form-control form-control-sm @error('customer_name') is-invalid @enderror"
                                    value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-6">
                                <label for="is_active" class="form-label small">Status *</label>
                                <select name="is_active" id="is_active"
                                    class="form-select form-select-sm @error('is_active') is-invalid @enderror" required>
                                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="image" class="form-label small">Image <span
                                        class="text-danger">*</span></label>
                                <input type="file" name="customer_image" id="customer_image"
                                    class="form-control form-control-sm @error('image') is-invalid @enderror">
                                @error('image')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="rating" class="form-label small">Rating (0.1 - 5.0)*</label>
                                <input type="text" name="rating" id="rating" placeholder="Enter the rating.."
                                    class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                    value="{{ old('rating') }}">
                                @error('rating')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-12">
                                <label for="testimonial" class="form-label small">Comment</label>
                                <textarea name="testimonial" id="testimonial" placeholder="Enter the comment.."
                                    class="form-control form-control-sm @error('testimonial') is-invalid @enderror" rows="2">{{ old('address') }}</textarea>
                                @error('testimonial')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">Create testimonial</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
