@extends('admin.layouts.app')

@section('title', 'Edit ' . $gateway->display_name . ' - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit {{ $gateway->display_name }} Settings</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.gateways.index') }}">Gateway Settings</a></li>
                    <li class="breadcrumb-item active">Edit {{ $gateway->display_name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('admin.gateways.update', $gateway->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Gateway Info --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-information-line me-1"></i> Gateway Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gateway Name</label>
                            <input type="text" class="form-control" value="{{ $gateway->display_name }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <input type="text" class="form-control" value="{{ ucfirst($gateway->type) }}" disabled>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ======================== PAYMENT GATEWAYS (Razorpay / Stripe) ======================== --}}
            
            @if(in_array($gateway->name, ['razorpay', 'stripe']))

            {{-- Test Credentials --}}
            <div class="card border border-warning">
                <div class="card-header bg-warning-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-flask-line me-1"></i> Test Credentials
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="test_key" class="form-label">Test Key</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="test_key" name="test_key"
                                   value="{{ $gateway->test_key ?? '' }}"
                                   placeholder="Enter test key">
                            <button class="btn btn-outline-secondary toggle-visibility" type="button" data-target="test_key">
                                <i class="ri-eye-off-line"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label for="test_secret" class="form-label">Test Secret</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="test_secret" name="test_secret"
                                   value="{{ $gateway->test_secret ?? '' }}"
                                   placeholder="Enter test secret">
                            <button class="btn btn-outline-secondary toggle-visibility" type="button" data-target="test_secret">
                                <i class="ri-eye-off-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Live Credentials --}}
            <div class="card border border-success">
                <div class="card-header bg-success-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-shield-check-line me-1"></i> Live Credentials
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="live_key" class="form-label">Live Key</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="live_key" name="live_key"
                                   value="{{ $gateway->live_key ?? '' }}"
                                   placeholder="Enter live key">
                            <button class="btn btn-outline-secondary toggle-visibility" type="button" data-target="live_key">
                                <i class="ri-eye-off-line"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label for="live_secret" class="form-label">Live Secret</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="live_secret" name="live_secret"
                                   value="{{ $gateway->live_secret ?? '' }}"
                                   placeholder="Enter live secret">
                            <button class="btn btn-outline-secondary toggle-visibility" type="button" data-target="live_secret">
                                <i class="ri-eye-off-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ======================== UPI GATEWAY ======================== --}}

            @elseif($gateway->name === 'upi')

            <div class="card border border-info">
                <div class="card-header bg-info-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-qr-code-line me-1"></i> UPI Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="upi_id" class="form-label">UPI ID</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="upi_id" name="upi_id"
                                   value="{{ $configData['upi_id'] ?? '' }}"
                                   placeholder="e.g. merchant@okicici">
                            <span class="input-group-text"><i class="ri-qr-code-line"></i></span>
                        </div>
                        <small class="text-muted">This UPI ID will be used for QR code generation and UPI payments.</small>
                    </div>

                    <div class="mb-0">
                        <label for="merchant_name" class="form-label">Merchant Name</label>
                        <input type="text" class="form-control" id="merchant_name" name="merchant_name"
                               value="{{ $configData['merchant_name'] ?? '' }}"
                               placeholder="e.g. Amit Dairy & Sweets">
                        <small class="text-muted">Displayed to customers when they scan the QR code.</small>
                    </div>
                </div>
            </div>

            @endif

            {{-- Submit --}}
            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i> Save Settings
                </button>
                <a href="{{ route('admin.gateways.index') }}" class="btn btn-soft-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Back
                </a>
            </div>
        </form>
    </div>

    {{-- Info Sidebar --}}
    <div class="col-lg-4">
        <div class="card border border-info">
            <div class="card-header bg-info-subtle">
                <h6 class="card-title mb-0"><i class="ri-lightbulb-line me-1"></i> How It Works</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @if(in_array($gateway->name, ['razorpay', 'stripe']))
                    <li class="mb-2 text-primary fw-semibold">
                        <i class="ri-information-line me-1"></i>
                        Note: Mode (Test/Live) is managed via the toggle on the <a href="{{ route('admin.gateways.index') }}">Gateway Settings</a> list.
                    </li>
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-success me-1"></i>
                        <strong>Test Mode:</strong> Uses test key & secret for sandbox transactions.
                    </li>
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-success me-1"></i>
                        <strong>Live Mode:</strong> Uses live key & secret for real transactions.
                    </li>
                    @elseif($gateway->name === 'upi')
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-success me-1"></i>
                        <strong>UPI ID:</strong> Your verified merchant UPI address.
                    </li>
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-success me-1"></i>
                        <strong>QR Codes:</strong> Auto-generated using this UPI ID for payments.
                    </li>
                    @endif
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-success me-1"></i>
                        Changes apply <strong>instantly</strong> — no server restart needed.
                    </li>
                    <li class="mb-0">
                        <i class="ri-shield-keyhole-line text-primary me-1"></i>
                        All credentials are <strong>encrypted</strong> in the database.
                    </li>
                </ul>
            </div>
        </div>

        @if(in_array($gateway->name, ['razorpay', 'stripe']))
        <div class="card border border-danger">
            <div class="card-header bg-danger-subtle">
                <h6 class="card-title mb-0"><i class="ri-alarm-warning-line me-1"></i> Important</h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">
                    Switching to <strong>Live Mode</strong> will process real payments.
                    Make sure your live credentials are correct before switching.
                </p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility (eye icon)
    document.querySelectorAll('.toggle-visibility').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ri-eye-off-line');
                icon.classList.add('ri-eye-line');
            } else {
                input.type = 'password';
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-off-line');
            }
        });
    });
</script>
@endpush
