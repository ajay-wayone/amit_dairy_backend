@extends('admin.layouts.app')

@section('title', 'Add Subscription - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Subscription</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.subscriptions.update', $subscription->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="plan_name" class="form-label small">Plan Name *</label>
                                <input type="text" name="plan_name" id="plan_name" placeholder="Enter The Plan_Name..."
                                    class="form-control form-control-sm @error('plan_name') is-invalid @enderror"
                                    value="{{ old('plan_name', $subscription->plan_name) }}">
                                @error('plan_name')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>



                            <div class="col-md-6">
                                <label for="amount" class="form-label small">Valid Days *</label>
                                <input type="number" name="valid_days" id="valid_days"
                                    placeholder="Enter the valid_days..."
                                    class="form-control form-control-sm @error('valid_days') is-invalid @enderror"
                                    value="{{ old('valid_days', $subscription->valid_days) }}" step="0.01">
                                @error('valid_days')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-6">
                                <label for="amount" class="form-label small">Amount (₹) *</label>
                                <input type="number" name="amount" id="amount" placeholder="Enter the Amount..."
                                    class="form-control form-control-sm @error('amount') is-invalid @enderror"
                                    value="{{ old('amount', $subscription->amount) }}" step="0.01">
                                @error('amount')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="is_active" class="form-label small">Status *</label>
                                <select name="is_active" id="is_active"
                                    class="form-select form-select-sm @error('is_active') is-invalid @enderror">
                                    <option value="1"
                                        {{ old('is_active', $subscription->is_active) == '1' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0"
                                        {{ old('is_active', $subscription->is_active) == '0' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="image" class="form-label small">Image *</label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="file" name="image" id="image"
                                        class="form-control form-control-sm @error('image') is-invalid @enderror"
                                        value="{{ old('image', $subscription->image) }}">
                                    @error('image')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror

                                    <img id="imagePreview"
                                        src="{{ $subscription->image ? asset('storage/' . $subscription->image) : '#' }}"
                                        alt="Image Preview"
                                        style="max-width: 150px; max-height: 100px; display: {{ $subscription->image ? 'block' : 'none' }}; border: 1px solid #ddd; padding: 5px; border-radius: 4px;" />
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label small">Description</label>
                                <textarea name="description" id="description" placeholder="Enter the description..."
                                    class="form-control form-control-sm @error('description') is-invalid @enderror" rows="2">{{ old('description', $subscription->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">Update Subscription</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection




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
