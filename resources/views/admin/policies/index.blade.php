@extends('admin.layouts.app')

@section('title', 'Policies Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Policies Management</h4>
                    <div class="page-title-right">
                        <a href="{{ route('admin.policies.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Add New Policy
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Status</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($policies as $policy)
                                        <tr>
                                            <td>
                                                <span class="badge text-warning">{{ $policy->type_label }}</span>
                                            </td>
                                            <td>{{ $policy->title }}</td>
                                                <td>{{ trim(html_entity_decode(strip_tags($policy->content))) }}</td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input toggle-status" type="checkbox"
                                                        data-id="{{ $policy->id }}"
                                                        {{ $policy->is_active ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>{{ $policy->updated_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Policy Actions">
                                                    <!-- Edit Button -->
                                                    <a href="{{ route('admin.policies.edit', $policy->id) }}"
                                                        class="btn btn-sm btn-outline-info d-flex align-items-center justify-content-center p-1 me-2"
                                                        title="Edit" style="height:20px; width:22px;">
                                                        <i class="bi bi-pencil fs-6"></i>
                                                    </a>

                                                    <!-- Delete Button -->
                                                    <form action="{{ route('admin.policies.destroy', $policy) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this policy?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center p-1"
                                                            title="Delete" style="height:20px; width:22px;">
                                                            <i class="bi bi-trash fs-6"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No policies found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <script>
        $(document).ready(function() {
            // Toggle policy status
            $('.toggle-status').on('change', function() {
                const policyId = $(this).data('id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: `/admin/policies/${policyId}/toggle-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error('Failed to update status');
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong!');
                        // Revert the checkbox
                        $(this).prop('checked', !isChecked);
                    }
                });
            });
        });
    </script>
@endpush
