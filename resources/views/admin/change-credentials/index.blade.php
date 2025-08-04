@extends('admin.layouts.app')

@section('title', 'Change Credentials - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Change Credentials</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.change-credentials.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="current_password" class="form-label small">Current Password *</label>
                                <input type="password" name="current_password" placeholder="Enter the current password..."
                                    id="current_password"
                                    class="form-control form-control-sm @error('current_password') is-invalid @enderror"
                                    required>
                                @error('current_password')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="new_password" class="form-label small">New Password *</label>
                                <input type="password" name="new_password" id="new_password"
                                    placeholder="Enter the new password..."
                                    class="form-control form-control-sm @error('new_password') is-invalid @enderror"
                                    required>
                                @error('new_password')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="new_password_confirmation" class="form-label small">Confirm New Password
                                    *</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                    placeholder="Enter the confirm_password..."
                                    class="form-control form-control-sm @error('new_password_confirmation') is-invalid @enderror"
                                    required>
                                @error('new_password_confirmation')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-primary btn-sm">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
