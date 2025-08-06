@extends('admin.layouts.app')

@section('title', 'Return Policy - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Return Policy</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.policies.return.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="content" class="form-label small">Return Policy Content</label>
                            <textarea name="content" id="content" class="form-control form-control-sm" rows="10"
                                placeholder="Enter return policy content...">{{ old('content', $return ?? '') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Update Return Policy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        CKEDITOR.replace('content', {
            height: 300
        });
        CKEDITOR.disableAutoInline = true;
        CKEDITOR.config.versionCheck = false;
    </script>
@endsection
