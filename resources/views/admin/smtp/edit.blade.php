@extends('admin.layouts.app')

@section('title', 'Configure SMTP - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Configure SMTP</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Configure SMTP</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ri-check-double-line me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('admin.smtp.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- SMTP Status --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ri-mail-settings-line me-1"></i> SMTP Status
                    </h5>
                    <div class="form-check form-switch form-switch-lg">
                        <input class="form-check-input" type="checkbox" id="smtpToggle"
                               {{ ($gateway && $gateway->active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="smtpToggle">
                            <span id="statusBadge" class="badge {{ ($gateway && $gateway->active) ? 'bg-success' : 'bg-danger' }}">
                                {{ ($gateway && $gateway->active) ? 'Active' : 'Inactive' }}
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- SMTP Configuration --}}
            <div class="card border border-primary">
                <div class="card-header bg-primary-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-server-line me-1"></i> Server Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="smtp_host" class="form-label">SMTP Host <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="smtp_host" name="smtp_host"
                                   value="{{ $configData['host'] ?? 'smtp.gmail.com' }}"
                                   placeholder="e.g. smtp.gmail.com" required>
                            @error('smtp_host')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="smtp_port" class="form-label">Port <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="smtp_port" name="smtp_port"
                                   value="{{ $configData['port'] ?? 587 }}"
                                   placeholder="587" required>
                            @error('smtp_port')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="smtp_encryption" class="form-label">Encryption <span class="text-danger">*</span></label>
                        <select class="form-select" id="smtp_encryption" name="smtp_encryption" required>
                            <option value="tls" {{ ($configData['encryption'] ?? '') === 'tls' ? 'selected' : '' }}>TLS (Port 587)</option>
                            <option value="ssl" {{ ($configData['encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                            <option value="none" {{ ($configData['encryption'] ?? '') === 'none' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Authentication --}}
            <div class="card border border-warning">
                <div class="card-header bg-warning-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-lock-line me-1"></i> Authentication
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="smtp_username" class="form-label">Username (Email) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="smtp_username" name="smtp_username"
                                   value="{{ $configData['username'] ?? '' }}"
                                   placeholder="your@gmail.com" required>
                            <span class="input-group-text"><i class="ri-mail-line"></i></span>
                        </div>
                        @error('smtp_username')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label for="smtp_password" class="form-label">Password / App Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="smtp_password" name="smtp_password"
                                   value="{{ $configData['password'] ?? '' }}"
                                   placeholder="Enter SMTP password or app password" required>
                            <button class="btn btn-outline-secondary toggle-visibility" type="button" data-target="smtp_password">
                                <i class="ri-eye-off-line"></i>
                            </button>
                        </div>
                        @error('smtp_password')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Sender Info --}}
            <div class="card border border-success">
                <div class="card-header bg-success-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-user-line me-1"></i> Sender Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="smtp_from_email" class="form-label">From Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="smtp_from_email" name="smtp_from_email"
                                   value="{{ $configData['from_email'] ?? '' }}"
                                   placeholder="no-reply@yourdomain.com" required>
                            @error('smtp_from_email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="smtp_from_name" class="form-label">From Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="smtp_from_name" name="smtp_from_name"
                                   value="{{ $configData['from_name'] ?? '' }}"
                                   placeholder="Amit Dairy & Sweets" required>
                            @error('smtp_from_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i> Save SMTP Settings
                </button>
            </div>
        </form>
    </div>

    {{-- Info Sidebar --}}
    <div class="col-lg-4">
        <div class="card border border-info">
            <div class="card-header bg-info-subtle">
                <h6 class="card-title mb-0"><i class="ri-lightbulb-line me-1"></i> Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-success me-1"></i>
                        <strong>Gmail:</strong> Use App Password (not your login password). Enable 2FA first.
                    </li>
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-success me-1"></i>
                        <strong>Port 587:</strong> TLS encryption. Most common for Gmail.
                    </li>
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-success me-1"></i>
                        <strong>Port 465:</strong> SSL encryption. Use if TLS doesn't work.
                    </li>
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-success me-1"></i>
                        Changes apply <strong>instantly</strong> — no server restart needed.
                    </li>
                    <li class="mb-0">
                        <i class="ri-shield-keyhole-line text-primary me-1"></i>
                        Password is <strong>encrypted</strong> in the database.
                    </li>
                </ul>
            </div>
        </div>

        <div class="card border border-warning">
            <div class="card-header bg-warning-subtle">
                <h6 class="card-title mb-0"><i class="ri-mail-check-line me-1"></i> Used For</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="ri-arrow-right-s-line text-primary me-1"></i> Newsletter subscription confirmations</li>
                    <li class="mb-2"><i class="ri-arrow-right-s-line text-primary me-1"></i> Order confirmation emails</li>
                    <li class="mb-0"><i class="ri-arrow-right-s-line text-primary me-1"></i> Admin delivery notifications</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
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

    // SMTP toggle status (AJAX)
    document.getElementById('smtpToggle').addEventListener('change', function() {
        fetch("{{ route('admin.smtp.toggle-status') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('statusBadge');
            if (data.success) {
                badge.textContent = data.active ? 'Active' : 'Inactive';
                badge.className = 'badge ' + (data.active ? 'bg-success' : 'bg-danger');
            }
        });
    });
</script>
@endpush
