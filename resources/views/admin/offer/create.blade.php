@extends('admin.layouts.app')

@section('title', 'Offer Settings - Admin Dashboard')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Add New Offer</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Offers</li>
                    <li class="breadcrumb-item active">Add Offer</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- OFFER FORM -->
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Offer Information</h4>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.offer.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Offer Name</label>
                        <input type="text" name="offer" class="form-control"
                               placeholder="Enter Offer (Ex: Summer Sale)"
                               value="{{ old('offer') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Coupon Code</label>
                        <input type="text" name="coupon_code" class="form-control"
                               placeholder="Enter Coupon Code (Ex: SUMMER30)"
                               value="{{ old('coupon_code') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Discount Percentage (%)</label>
                        <input type="number" name="discount_percentage" class="form-control"
                               placeholder="Enter Percentage (0-100)"
                               value="{{ old('discount_percentage') }}" required min="0" max="100" step="0.01">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Max Discount (₹)</label>
                        <input type="number" name="max_discount" class="form-control"
                               placeholder="Enter Max Discount"
                               value="{{ old('max_discount', 500) }}" required min="0">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" value="1" checked>
                            <label class="form-check-label">Active Status</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button class="btn btn-primary">Create Offer</button>
            </div>
        </form>
    </div>
</div>

<!-- OFFERS LIST -->
<div class="card mt-4">
    <div class="card-header">
        <h4 class="card-title">Offers List</h4>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Sr No.</th>
                    <th>Offer Name</th>
                    <th>Coupon Code</th>
                    <th>Discount</th>
                    <th>Max Disc.</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($offers as $offer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $offer->offer }}</td>
                    <td><code>{{ $offer->coupon_code }}</code></td>
                    <td>{{ (float)$offer->discount_percentage }}%</td>
                    <td>₹{{ number_format($offer->max_discount, 2) }}</td>
                    <td>
                        <span class="badge {{ $offer->status ? 'bg-success' : 'bg-danger' }}">
                            {{ $offer->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>{{ $offer->created_at->format('d M Y') }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editOfferModal{{ $offer->id }}">
                            Edit
                        </button>

                        <form action="{{ route('admin.offer.destroy', $offer->id) }}"
                              method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>

                <!-- EDIT OFFER MODAL -->
                <div class="modal fade" id="editOfferModal{{ $offer->id }}">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('admin.offer.update', $offer->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title">Edit Offer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Offer Name</label>
                                        <input type="text" name="offer" class="form-control"
                                               value="{{ $offer->offer }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Coupon Code</label>
                                        <input type="text" name="coupon_code" class="form-control"
                                               value="{{ $offer->coupon_code }}" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Discount (%)</label>
                                                <input type="number" name="discount_percentage" class="form-control"
                                                       value="{{ (float)$offer->discount_percentage }}" required min="0" max="100" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Max Discount (₹)</label>
                                                <input type="number" name="max_discount" class="form-control"
                                                       value="{{ $offer->max_discount }}" required min="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="status" value="1" {{ $offer->status ? 'checked' : '' }}>
                                            <label class="form-check-label">Active Status</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-warning">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="4" class="text-center">No offers found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- PAYMENT SLABS -->
<div class="card mt-4">
    <div class="card-header">
        <h4 class="card-title"> Add Payment Slabs</h4>
    </div>

    <div class="card-body">

        <!-- Add Slab -->
        <form action="{{ route('admin.slabs.store') }}" method="POST">
            @csrf
            {{-- <div class="row mb-3">
                <div class="col-md-3">
                    <input type="number" name="min_km" class="form-control" placeholder="Min Kg" required>
                </div>

                <div class="col-md-3">
                    <input type="number" name="max_km" class="form-control" placeholder="Max Kg">
                </div>

                <div class="col-md-3">
                    <input type="number" name="advance_percentage" class="form-control" placeholder="Advance %" required>
                </div>

                <div class="col-md-2 d-flex align-items-center">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="status" value="1">
        <label class="form-check-label">Active</label>
    </div>
</div>
<br>

                <div class="col-md-3">
                    <button class="btn btn-primary w-100">Add Slab</button>
                </div>
            </div> --}}


            <div class="row mb-3">
    <div class="col-md-2">
        <input type="number" name="min_km" class="form-control" placeholder="Min Kg" required>
    </div>

    <div class="col-md-2">
        <input type="number" name="max_km" class="form-control" placeholder="Max Kg">
    </div>

    <div class="col-md-2">
        <input type="number" name="advance_percentage" class="form-control" placeholder="Advance %" required>
    </div>

    <div class="col-md-2 d-flex align-items-center">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="status" value="1">
            <label class="form-check-label">Active</label>
        </div>
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary w-100">Add Slab</button>
    </div>
</div>

        </form>

        <!-- Slab Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Min Kg</th>
                    <th>Max Kg</th>
                    <th>Advance %</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($slabs as $slab)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $slab->min_km }}</td>
                    <td>{{ $slab->max_km }}</td>
                    <td>{{ $slab->advance_percentage }}%</td>
                    <td>
                        <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editSlabModal{{ $slab->id }}">
                            Edit
                        </button>

                        <form action="{{ route('admin.slabs.delete', $slab->id) }}"
                              method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>

                <!-- EDIT SLAB MODAL -->
                <div class="modal fade" id="editSlabModal{{ $slab->id }}">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('admin.slabs.update', $slab->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title">Edit Slab</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <input type="number" name="min_km" class="form-control mb-2"
                                           value="{{ $slab->min_km }}" required>

                                    <input type="number" name="max_km" class="form-control mb-2"
                                           value="{{ $slab->max_km }}">

                                    <input type="number" name="advance_percentage"
                                           class="form-control"
                                           value="{{ $slab->advance_percentage }}" required>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-warning">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection
