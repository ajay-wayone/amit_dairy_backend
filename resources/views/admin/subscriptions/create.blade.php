@extends('admin.layouts.app')

@section('title', 'Add Subscription - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Add New Subscription</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.subscriptions.store') }}">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label small">Customer *</label>
                            <select name="customer_id" id="customer_id" class="form-select form-select-sm @error('customer_id') is-invalid @enderror" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="plan_name" class="form-label small">Plan Name *</label>
                            <input type="text" name="plan_name" id="plan_name" class="form-control form-control-sm @error('plan_name') is-invalid @enderror" value="{{ old('plan_name') }}" required>
                            @error('plan_name')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="start_date" class="form-label small">Start Date *</label>
                            <input type="date" name="start_date" id="start_date" class="form-control form-control-sm @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                            @error('start_date')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="end_date" class="form-label small">End Date *</label>
                            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                            @error('end_date')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="amount" class="form-label small">Amount (₹) *</label>
                            <input type="number" name="amount" id="amount" class="form-control form-control-sm @error('amount') is-invalid @enderror" value="{{ old('amount') }}" step="0.01" required>
                            @error('amount')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="is_active" class="form-label small">Status *</label>
                            <select name="is_active" id="is_active" class="form-select form-select-sm @error('is_active') is-invalid @enderror" required>
                                <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="description" class="form-label small">Description</label>
                            <textarea name="description" id="description" class="form-control form-control-sm @error('description') is-invalid @enderror" rows="2">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm">Create Subscription</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 