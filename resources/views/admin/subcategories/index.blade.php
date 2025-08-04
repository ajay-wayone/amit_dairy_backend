@extends('admin.layouts.app')

@section('title', 'Subcategories - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Subcategories</h4>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.subcategories.index') }}" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search subcategories..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.subcategories.create') }}" class="btn btn-success">
                            <i class="ri-add-line align-middle me-1"></i> Add Subcategory
                        </a>
                    </div>
                </div>

                <!-- Subcategories Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subcategories as $subcategory)
                            <tr>
                                <td>{{ $subcategory->id }}</td>
                                <td>{{ $subcategory->name }}</td>
                                <td>{{ $subcategory->category->name ?? 'N/A' }}</td>
                                <td>{{ Str::limit($subcategory->description, 100) }}</td>
                                <td>
                                    <span class="badge bg-{{ $subcategory->is_active ? 'success' : 'danger' }}">
                                        {{ $subcategory->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $subcategory->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.subcategories.edit', $subcategory->id) }}" class="btn btn-sm btn-warning">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-item" 
                                                data-id="{{ $subcategory->id }}" 
                                                data-name="{{ $subcategory->name }}"
                                                data-type="subcategory"
                                                data-url="{{ route('admin.subcategories.destroy', $subcategory->id) }}">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No subcategories found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($subcategories->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $subcategories->firstItem() }} to {{ $subcategories->lastItem() }} of {{ $subcategories->total() }} entries
                    </div>
                    <div class="pagination-container">
                        {{ $subcategories->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 