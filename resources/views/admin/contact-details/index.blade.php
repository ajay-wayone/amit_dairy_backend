@extends('admin.layouts.app')

@section('title', 'Website Settings - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Website Settings</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.contact-details.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="company_name" class="form-label small">Company Name *</label>
                            <input type="text" name="company_name" id="company_name" class="form-control form-control-sm @error('company_name') is-invalid @enderror" value="{{ old('company_name', $settings->company_name ?? '') }}" required>
                            @error('company_name')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="phone" class="form-label small">Phone Number *</label>
                            <input type="text" name="phone" id="phone" class="form-control form-control-sm @error('phone') is-invalid @enderror" value="{{ old('phone', $settings->phone ?? '') }}" required>
                            @error('phone')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email" class="form-label small">Email Address *</label>
                            <input type="email" name="email" id="email" class="form-control form-control-sm @error('email') is-invalid @enderror" value="{{ old('email', $settings->email ?? '') }}" required>
                            @error('email')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="address" class="form-label small">Address *</label>
                            <input type="text" name="address" id="address" class="form-control form-control-sm @error('address') is-invalid @enderror" value="{{ old('address', $settings->address ?? '') }}" required>
                            @error('address')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="facebook" class="form-label small">Facebook URL</label>
                            <input type="url" name="facebook" id="facebook" class="form-control form-control-sm @error('facebook') is-invalid @enderror" value="{{ old('facebook', $settings->facebook ?? '') }}">
                            @error('facebook')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="instagram" class="form-label small">Instagram URL</label>
                            <input type="url" name="instagram" id="instagram" class="form-control form-control-sm @error('instagram') is-invalid @enderror" value="{{ old('instagram', $settings->instagram ?? '') }}">
                            @error('instagram')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="twitter" class="form-label small">Twitter URL</label>
                            <input type="url" name="twitter" id="twitter" class="form-control form-control-sm @error('twitter') is-invalid @enderror" value="{{ old('twitter', $settings->twitter ?? '') }}">
                            @error('twitter')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="youtube" class="form-label small">YouTube URL</label>
                            <input type="url" name="youtube" id="youtube" class="form-control form-control-sm @error('youtube') is-invalid @enderror" value="{{ old('youtube', $settings->youtube ?? '') }}">
                            @error('youtube')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="about_us" class="form-label small">About Us</label>
                            <textarea name="about_us" id="about_us" class="form-control form-control-sm @error('about_us') is-invalid @enderror" rows="3">{{ old('about_us', $settings->about_us ?? '') }}</textarea>
                            @error('about_us')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm">Update Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 