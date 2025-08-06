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
                                <input type="text" name="search" class="form-control me-2"
                                    placeholder="Search subcategories..." value="{{ request('search') }}">
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
                                    <th>Sr.n</th>
                                    <th>Image</th>
                                    <th>Category</th>
                                    <th>Subcategory</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subcategories as $key=> $subcategory)
                                    <tr>
                                        <td>{{ $subcategory->$key + 1 }}</td>
                                        <td>
                                            @if ($subcategory->subcategory_image)
                                                <img src="{{ asset('storage/' . $subcategory->subcategory_image) }}"
                                                    alt="{{ $subcategory->subcategory_name }}" class="category-image"
                                                    width="100">
                                            @else
                                                <div
                                                    class="category-image bg-light d-flex align-items-center justify-content-center">
                                                    <i class="ri-image-line text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $subcategory->category->name ?? 'N/A' }}</td>
                                        <td>{{ Str::limit($subcategory->subcategory_name) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $subcategory->is_active ? 'success' : 'danger' }}">
                                                {{ $subcategory->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $subcategory->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.subcategories.edit', $subcategory->id) }}"
                                                    class="btn btn-sm btn-warning me-2">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-item"
                                                    data-id="{{ $subcategory->id }}" data-name="{{ $subcategory->name }}"
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
                    @if ($subcategories->hasPages())
                        <nav aria-label="d-flex justify-content-end mt-3">
                            <ul class="pagination">
                                {{-- Previous Page Link --}}
                                @if ($subcategories->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">Previous</span></li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $subcategories->previousPageUrl() }}"
                                            rel="prev">Previous</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($subcategories->getUrlRange(1, $subcategories->lastPage()) as $page => $url)
                                    @if ($page == $subcategories->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($subcategories->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $subcategories->nextPageUrl() }}"
                                            rel="next">Next</a>
                                    </li>
                                @else
                                    <li class="page-item disabled"><span class="page-link">Next</span></li>
                                @endif
                            </ul>
                        </nav>
                    @endif

                </div>
            </div>
        </div>
    </div>


@endsection
