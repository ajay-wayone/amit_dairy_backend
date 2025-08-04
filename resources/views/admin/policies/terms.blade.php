@extends('admin.layouts.app')

@section('title', 'Terms & Conditions - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Terms & Conditions</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.policies.terms.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="content" class="form-label small">Terms & Conditions Content</label>
                        <textarea name="content" id="content" class="form-control form-control-sm" rows="10" placeholder="Enter terms and conditions content...">{{ old('content', $terms ?? '') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Update Terms</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 