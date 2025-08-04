@extends('admin.layouts.app')

@section('title', 'Add box - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Edit box</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.boxes.index') }}">boxes</a></li>
                        <li class="breadcrumb-item active">Edit box</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> Edit box Information</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.boxes.update', $box->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- Left Side -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="box_name" class="form-label">Box Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('box_name') is-invalid @enderror"
                                        id="box_name" name="box_name" placeholder="Enter the box name"
                                        value="{{ old('box_name', $box->box_name) }}">
                                    @error('box_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="box_price" class="form-label">Box Price <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('box_price') is-invalid @enderror"
                                        id="box_price" name="box_price" placeholder="Enter the box price"
                                        value="{{ old('box_price', $box->box_price) }}">
                                    @error('box_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            {{ old('is_active', $box->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="box_image" class="form-label">Box Image <span
                                            class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('box_image') is-invalid @enderror"
                                        id="box_image" name="box_image" accept="image/*">
                                    @error('box_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if ($box->box_image)
                                    <div class="mb-3" id="imagePreview">

                                        <img src="{{ asset('storage/' . $box->box_image) }}" alt="{{ $box->name }}"
                                            class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <input type="hidden" name="is_active" value="0">

                        <!-- Submit & Cancel Buttons -->
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end gap-2 mb-3">
                                <a href="{{ route('admin.boxes.index') }}" class="btn btn-secondary">
                                    <i class="ri-arrow-left-line me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i> Update
                                </button>
                            </div>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-check-input:checked {
            background-color: #556ee6;
            border-color: #556ee6;
        }

        #imagePreview {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        #previewImg {
            max-width: 100%;
            height: auto;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Image preview
            $('#box_image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewImg').attr('src', e.target.result);
                        $('#imagePreview').removeClass('d-none');
                    }
                    reader.readAsDataURL(file);
                } else {
                    $('#imagePreview').addClass('d-none');
                }
            });

            // Form validation
            $('form').on('submit', function() {
                const name = $('#box_name').val().trim();
                const image = $('#box_image').val();



                return true;
            });

        });
    </script>
@endpush
