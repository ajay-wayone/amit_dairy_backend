@extends('admin.layouts.app')

@section('title', 'Edit Delivery Location - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Edit Delivery Location</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.delivery-locations.update', $deliveryLocation->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                       <div class="col-12">
                            <label for="address" class="form-label small">Location *</label>
                            <textarea name="location" id="location" class="form-control form-control-sm @error('location') is-invalid @enderror" rows="3" required>{{ old('location', $deliveryLocation->location) }}</textarea>
                            @error('location')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="pincode" class="form-label small">Pincode</label>
                            <input type="text" name="pincode" id="pincode" class="form-control form-control-sm @error('pincode') is-invalid @enderror" value="{{ old('pincode', $deliveryLocation->pincode) }}">
                            @error('pincode')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                       
                        
                        <div class="col-md-6">
                            <label for="is_active" class="form-label small">Status *</label>
                            <select name="is_active" id="is_active" class="form-select form-select-sm @error('is_active') is-invalid @enderror" required>
                                <option value="1" {{ old('is_active', $deliveryLocation->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $deliveryLocation->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('admin.delivery-locations.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm">Update Location</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 