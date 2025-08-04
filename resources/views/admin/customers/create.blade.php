@extends('admin.layouts.app')

@section('title', 'Add Customer - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Add New Customer</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.customers.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label small">Full Name *</label>
                                <input type="text" name="name" id="name" placeholder="Enter the name..."
                                    class="form-control form-control-sm @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label small">Email Address *</label>
                                <input type="email" name="email" id="email" placeholder="Enter the email..."
                                    class="form-control form-control-sm @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label small">Phone Number</label>
                                <input type="text" name="phone" id="phone" placeholder="Enter the phone..."
                                    class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="is_active" class="form-label small">Status *</label>
                                <select name="is_active" id="is_active"
                                    class="form-select form-select-sm @error('is_active') is-invalid @enderror" required>
                                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label small">Address</label>
                                <textarea name="address" id="address" placeholder="Enter the address..."
                                    class="form-control form-control-sm  @error('address') is-invalid @enderror" rows="2">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">Create Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
