@extends('admin.layouts.app')

@section('title', ucfirst($policy->type) . ' Policy')

@section('content')
    <div class="container mt-4">
        <h4>{{ ucfirst($policy->type) }} Policy</h4>

        @if (session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('policies.update', $policy->type) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Content</label>
                <textarea name="content" class="form-control" rows="10">{{ old('content', $policy->content) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update {{ ucfirst($policy->type) }} Policy</button>
        </form>
    </div>
@endsection
