@extends('admin.layouts.app')

@section('title', 'Privacy Policy - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Privacy Policy</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.policies.privacy.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="content" class="form-label small">Privacy Policy Content</label>
                        <textarea name="content" id="content" class="form-control form-control-sm" rows="10" placeholder="Enter privacy policy content...">{{ old('content', $privacy ?? '') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Update Privacy Policy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 