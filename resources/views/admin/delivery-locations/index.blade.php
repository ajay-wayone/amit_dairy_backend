@extends('admin.layouts.app')

@section('title', 'Delivery Locations - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Delivery Locations</h4>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.delivery-locations.index') }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2"
                                    placeholder="Search locations..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.delivery-locations.create') }}" class="btn btn-success">
                                <i class="ri-add-line align-middle me-1"></i> Add Location
                            </a>
                        </div>
                    </div>

                    <!-- Delivery Locations Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Location Name</th>
                                    <th>Pincodde</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deliveryLocations as $location)
                                    <tr>
                                        <td>{{ $location->id }}</td>
                                        <td>{{ Str::limit($location->location, 100) }}</td>
                                        <td>{{ Str::limit($location->pincode) }}</td>

                                       
                                        <td>
                                            <span class="badge bg-{{ $location->is_active ? 'success' : 'danger' }}">
                                                {{ $location->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $location->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.delivery-locations.edit', $location->id) }}"
                                                    class="btn btn-sm btn-warning me-2">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-item"
                                                    data-id="{{ $location->id }}" data-name="{{ $location->name }}"
                                                    data-type="delivery location"
                                                    data-url="{{ route('admin.delivery-locations.destroy', $location->id) }}">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No delivery locations found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($deliveryLocations->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $deliveryLocations->firstItem() }} to {{ $deliveryLocations->lastItem() }} of
                                {{ $deliveryLocations->total() }} entries
                            </div>
                            <div class="pagination-container">
                                {{ $deliveryLocations->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
