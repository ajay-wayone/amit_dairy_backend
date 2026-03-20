@extends('admin.layouts.app')

@section('title', 'Gateway Settings - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Gateway Settings</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Gateway Settings</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">Payment & SMS Gateways</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0" id="gatewayTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Gateway</th>
                                <th>Type</th>
                                <th>Mode</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gateways as $index => $gateway)
                            <tr id="gateway-row-{{ $gateway->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-2">
                                            <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-16">
                                                @if($gateway->name === 'razorpay')
                                                    <i class="ri-bank-card-line"></i>
                                                @elseif($gateway->name === 'stripe')
                                                    <i class="ri-visa-line"></i>
                                                @else
                                                    <i class="ri-settings-3-line"></i>
                                                @endif
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $gateway->display_name }}</h6>
                                            <small class="text-muted">{{ $gateway->name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">{{ ucfirst($gateway->type) }}</span>
                                </td>
                                <td>
                                    @if(in_array($gateway->name, ['razorpay', 'stripe']))
                                    <div class="form-check form-switch form-switch-md" dir="ltr">
                                        <input type="checkbox" class="form-check-input mode-toggle"
                                               id="modeSwitch{{ $gateway->id }}"
                                               data-id="{{ $gateway->id }}"
                                               {{ $gateway->mode === 'live' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="modeSwitch{{ $gateway->id }}" id="modeLabel{{ $gateway->id }}">
                                            @if($gateway->mode === 'live')
                                                <span class="badge bg-success">LIVE</span>
                                            @else
                                                <span class="badge bg-warning text-dark">TEST</span>
                                            @endif
                                        </label>
                                    </div>
                                    @else
                                    <span class="text-muted fs-12">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="form-check form-switch form-switch-md" dir="ltr">
                                        <input type="checkbox" class="form-check-input status-toggle"
                                               id="statusSwitch{{ $gateway->id }}"
                                               data-id="{{ $gateway->id }}"
                                               {{ $gateway->active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="statusSwitch{{ $gateway->id }}" id="statusLabel{{ $gateway->id }}">
                                            @if($gateway->active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.gateways.edit', $gateway->id) }}" class="btn btn-sm btn-soft-primary">
                                        <i class="ri-pencil-line"></i> Edit Credentials
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ri-inbox-line fs-24 d-block mb-2"></i>
                                        No gateways configured yet. Please run the seeder.
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Mode toggle (test ↔ live)
    $('.mode-toggle').on('change', function() {
        const id = $(this).data('id');
        const checkbox = $(this);
        const label = $('#modeLabel' + id);

        $.ajax({
            url: '{{ url("admin/gateways") }}/' + id + '/toggle-mode',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    if (response.mode === 'live') {
                        label.html('<span class="badge bg-success">LIVE</span>');
                    } else {
                        label.html('<span class="badge bg-warning text-dark">TEST</span>');
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Mode Changed',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            },
            error: function() {
                checkbox.prop('checked', !checkbox.prop('checked'));
                Swal.fire('Error', 'Failed to toggle mode.', 'error');
            }
        });
    });

    // Status toggle (active ↔ inactive)
    $('.status-toggle').on('change', function() {
        const id = $(this).data('id');
        const checkbox = $(this);
        const label = $('#statusLabel' + id);

        $.ajax({
            url: '{{ url("admin/gateways") }}/' + id + '/toggle-status',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    if (response.active) {
                        label.html('<span class="badge bg-success">Active</span>');
                    } else {
                        label.html('<span class="badge bg-danger">Inactive</span>');
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Changed',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            },
            error: function() {
                checkbox.prop('checked', !checkbox.prop('checked'));
                Swal.fire('Error', 'Failed to toggle status.', 'error');
            }
        });
    });
});
</script>
@endpush
