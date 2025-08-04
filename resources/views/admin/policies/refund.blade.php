@extends('admin.layouts.app')

@section('title', 'Refund Policy - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Refund Policy</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.policies.refund.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="content" class="form-label small">Refund Policy Content</label>
                        <textarea name="content" id="content" class="form-control form-control-sm" rows="10" placeholder="Enter refund policy content...">{{ old('content', $refund ?? '') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Update Refund Policy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 