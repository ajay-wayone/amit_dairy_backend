@extends('admin.layouts.app')

@section('title', 'Customer Details - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Customer Details</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="row g-2">

                                <div class="col-4"><strong class="small">Name:</strong></div>
                                <div class="col-8 small">{{ $customer->name }}</div>

                                <div class="col-4"><strong class="small">Email:</strong></div>
                                <div class="col-8 small">{{ $customer->email }}</div>

                                <div class="col-4"><strong class="small">Phone:</strong></div>
                                <div class="col-8 small">{{ $customer->phone ?? 'N/A' }}</div>

                                <div class="col-4"><strong class="small">Status:</strong></div>
                                <div class="col-8">
                                    <span class="badge bg-{{ $customer->is_active ? 'success' : 'danger' }} badge-sm">
                                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="col-4"><strong class="small">Created:</strong></div>
                                <div class="col-8 small">{{ $customer->created_at->format('M d, Y H:i') }}</div>

                                <div class="col-4"><strong class="small">Updated:</strong></div>
                                <div class="col-8 small">{{ $customer->updated_at->format('M d, Y H:i') }}</div>

                                <div class="col-4"><strong class="small">Address:</strong></div>
                                <div class="col-8 small">{{ $customer->address ?? 'No address provided' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">Edit
                            Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
