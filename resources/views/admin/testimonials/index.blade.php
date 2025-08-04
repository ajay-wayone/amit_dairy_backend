@extends('admin.layouts.app')

@section('title', 'Testimonials - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Testimonials</h4>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.testimonials.index') }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2"
                                    placeholder="Search testimonials..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary ">Search</button>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.testimonials.create') }}" class="btn btn-success btn-sm">
                                <i class="ri-add-line me-1"></i> Add Testimonial
                            </a>
                        </div>
                    </div>

                    <!-- Testimonials Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sr.n</th>
                                    <th>Image</th>
                                    <th>Customer Name</th>
                                    <th>Rating</th>
                                    <th>Comment</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($testimonials as $key => $testimonial)
                                    <tr>
                                        <td>{{ $testimonial->$key + 1 }}</td>
                                        <td>
                                            @if ($testimonial->customer_image)
                                                <img src="{{ asset('storage/' . $testimonial->customer_image) }}"
                                                    width="80">
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                        </td>
                                        <td>{{ $testimonial->customer_name }}</td>
                                        <td>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="ri-star-fill {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            <span class="ms-1">({{ $testimonial->rating }}/5)</span>
                                        </td>
                                        <td>{{ Str::limit($testimonial->testimonial, 100) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $testimonial->is_active ? 'success' : 'danger' }}">
                                                {{ $testimonial->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $testimonial->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}"
                                                    class="btn btn-sm btn-warning me-1">
                                                    <i class="ri-edit-line"></i>
                                                </a>

                                                <button type="button" class="btn btn-sm btn-danger delete-item "
                                                    data-id="{{ $testimonial->id }}"
                                                    data-name="{{ $testimonial->customer_name }}" data-type="testimonial"
                                                    data-url="{{ route('admin.testimonials.destroy', $testimonial->id) }}">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No testimonials found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($testimonials->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $testimonials->firstItem() }} to {{ $testimonials->lastItem() }} of
                                {{ $testimonials->total() }} entries
                            </div>
                            <div class="pagination-container">
                                {{ $testimonials->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
