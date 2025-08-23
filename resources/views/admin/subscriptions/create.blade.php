@extends('admin.layouts.app')

@section('title', 'Add Subscription - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Add New Subscription</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.subscriptions.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="plan_name" class="form-label small">Plan Name *</label>
                                <input type="text" name="plan_name" id="plan_name" placeholder="Enter The Plan_Name..."
                                    class="form-control form-control-sm @error('plan_name') is-invalid @enderror"
                                    value="{{ old('plan_name') }}" required>
                                @error('plan_name')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="valid_days" class="form-label small">Valid Days *</label>
                                <input type="number" name="valid_days" id="valid_days"
                                    placeholder="Enter the valid_days..."
                                    class="form-control form-control-sm @error('valid_days') is-invalid @enderror"
                                    value="{{ old('valid_days') }}" step="1" required>
                                @error('valid_days')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="amount" class="form-label small">Amount (₹) *</label>
                                <input type="number" name="amount" id="amount" placeholder="Enter the Amount..."
                                    class="form-control form-control-sm @error('amount') is-invalid @enderror"
                                    value="{{ old('amount') }}" step="0.01" required>
                                @error('amount')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="is_active" class="form-label small">Status *</label>
                                <select name="is_active" id="is_active"
                                    class="form-select form-select-sm @error('is_active') is-invalid @enderror" required>
                                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 ">
                                <label for="image" class="form-label small">Image *</label>
                                <input type="file" name="image" id="image"
                                    class="form-control form-control-sm @error('image') is-invalid @enderror" required>
                                @error('image')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex align-items-center gap-3 mt-2">
                                <img id="imagePreview" src="#" alt="Image Preview"
                                    style="max-width: 150px; display: none; border: 1px solid #ddd; padding: 5px; border-radius: 4px;" />
                            </div>


                            <div class="col-12">
                                <label for="description" class="form-label small">Description</label>
                                <textarea name="description" id="description" placeholder="Enter the description..."
                                    class="form-control form-control-sm @error('description') is-invalid @enderror" rows="2">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">Create Subscription</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('image').addEventListener('change', function(event) {
            const [file] = this.files;
            if (file) {
                const preview = document.getElementById('imagePreview');
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            }
        });
    </script>
@endpush
