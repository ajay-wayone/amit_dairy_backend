@extends('admin.layouts.app')

@section('title', 'Delivery Location Details - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Delivery Location Details</h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-4"><strong class="small">ID:</strong></div>
                            <div class="col-8 small">{{ $deliveryLocation->id }}</div>
                            
                            <div class="col-4"><strong class="small">Name:</strong></div>
                            <div class="col-8 small">{{ $deliveryLocation->name }}</div>
                            
                            <div class="col-4"><strong class="small">Phone:</strong></div>
                            <div class="col-8 small">{{ $deliveryLocation->phone ?? 'N/A' }}</div>
                            
                            <div class="col-4"><strong class="small">Status:</strong></div>
                            <div class="col-8">
                                <span class="badge bg-{{ $deliveryLocation->is_active ? 'success' : 'danger' }} badge-sm">
                                    {{ $deliveryLocation->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-4"><strong class="small">Created:</strong></div>
                            <div class="col-8 small">{{ $deliveryLocation->created_at->format('M d, Y H:i') }}</div>
                            
                            <div class="col-4"><strong class="small">Updated:</strong></div>
                            <div class="col-8 small">{{ $deliveryLocation->updated_at->format('M d, Y H:i') }}</div>
                            
                            <div class="col-4"><strong class="small">Address:</strong></div>
                            <div class="col-8 small">{{ $deliveryLocation->address }}</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('admin.delivery-locations.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    <a href="{{ route('admin.delivery-locations.edit', $deliveryLocation->id) }}" class="btn btn-warning btn-sm">Edit Location</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 