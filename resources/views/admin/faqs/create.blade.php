@extends('admin.layouts.app')

@section('title', 'Add Customer - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Add New Faq</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.faqs.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="question" class="form-label small">Question *</label>
                                <textarea name="question" id="question" rows="4" placeholder="Enter the question..."
                                    class="form-control form-control-sm @error('question') is-invalid @enderror" required>{{ old('question') }}</textarea>
                                @error('question')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-6">
                                <label for="answer" class="form-label small">Answer *</label>
                                <textarea name="answer" id="answer" placeholder="Enter the answer..." rows="4"
                                    class="form-control form-control-sm @error('answer') is-invalid @enderror" required>{{ old('answer') }}</textarea>
                                @error('answer')
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


                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">Create Faqs</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
