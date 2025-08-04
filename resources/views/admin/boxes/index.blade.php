@extends('admin.layouts.app')

@section('title', 'Boxes - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Boxes</h4>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.boxes.index') }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search boxes..."
                                    value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.boxes.create') }}" class="btn btn-success">
                                <i class="ri-add-line align-middle me-1"></i> Add Box
                            </a>
                        </div>
                    </div>

                    <!-- Boxes Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sr.n</th>
                                    <th>Box Image</th>
                                    <th>Box Name</th>
                                    <th>Box Price</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($boxes as $key=> $box)
                                    <tr>
                                        <td>{{ $box->$key + 1 }}</td>
                                        <td>
                                            @if ($box->box_image)
                                                <img src="{{ asset('storage/' . $box->box_image) }}"
                                                    alt="{{ $box->name }}" class="img-thumbnail"
                                                    style="max-width: 100px; max-height: 60px;">
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>
                                        <td>{{ $box->box_name }}</td>
                                        <td>{{ Str::limit($box->box_price, 100) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $box->is_active ? 'success' : 'danger' }}">
                                                {{ $box->is_active ? 'Active' : 'inctive' }}
                                            </span>
                                        </td>
                                        <td>{{ $box->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.boxes.edit', $box->id) }}"
                                                    class="btn btn-sm btn-warning me-2">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-item "
                                                    data-id="{{ $box->id }}" data-name="{{ $box->name }}"
                                                    data-type="box"
                                                    data-url="{{ route('admin.boxes.destroy', $box->id) }}">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No boxes found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($boxes->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $boxes->firstItem() }} to {{ $boxes->lastItem() }} of {{ $boxes->total() }}
                                entries
                            </div>
                            <div class="pagination-container">
                                {{ $boxes->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
