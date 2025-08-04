@extends('admin.layouts.app')

@section('title', 'FAQs - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Frequently Asked Questions</h4>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.faqs.index') }}" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search FAQs..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.faqs.create') }}" class="btn btn-success">
                            <i class="ri-add-line align-middle me-1"></i> Add FAQ
                        </a>
                    </div>
                </div>

                <!-- FAQs Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Order</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faqs as $faq)
                            <tr>
                                <td>{{ $faq->id }}</td>
                                <td>{{ Str::limit($faq->question, 100) }}</td>
                                <td>{{ Str::limit($faq->answer, 150) }}</td>
                                <td>{{ $faq->category ?? 'General' }}</td>
                                <td>
                                    <span class="badge bg-{{ $faq->is_active ? 'success' : 'danger' }}">
                                        {{ $faq->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $faq->order ?? 'N/A' }}</td>
                                <td>{{ $faq->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="btn btn-sm btn-warning">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-item" 
                                                data-id="{{ $faq->id }}" 
                                                data-name="{{ Str::limit($faq->question, 30) }}"
                                                data-type="FAQ"
                                                data-url="{{ route('admin.faqs.destroy', $faq->id) }}">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No FAQs found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($faqs->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $faqs->firstItem() }} to {{ $faqs->lastItem() }} of {{ $faqs->total() }} entries
                    </div>
                    <div class="pagination-container">
                        {{ $faqs->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 