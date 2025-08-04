@extends('admin.layouts.app')

@section('title', 'Manage Banner')

@section('content')
    <div class="row">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Manage Banner</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="submit_banner" value="1">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="pageSelect" class="form-label">Select Page</label>
                                <select name="page_name" id="pageSelect" class="form-select" onchange="this.form.submit()"
                                    required>
                                    <option value="">-- Select Page --</option>
                                    <option value="home"
                                        {{ old('page_name', $selectedPage) == 'home' ? 'selected' : '' }}>Home</option>
                                    <option value="about"
                                        {{ old('page_name', $selectedPage) == 'about' ? 'selected' : '' }}>About</option>
                                    <option value="product"
                                        {{ old('page_name', $selectedPage) == 'product' ? 'selected' : '' }}>Products
                                    </option>
                                    <option value="contact"
                                        {{ old('page_name', $selectedPage) == 'contact' ? 'selected' : '' }}>Contact
                                    </option>
                                    <!-- Add more if needed -->
                                </select>
                            </div>

                            @if (!empty($selectedPage))
                                @if (!empty($currentImage))
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Current Banner</label><br>
                                        <img src="{{ asset($currentImage) }}" alt="Current Banner"
                                            style="max-width: 100%; height: auto; border:1px solid #ccc;">
                                    </div>
                                @endif

                                <div class="col-md-6 mb-3">
                                    <label for="imageInput" class="form-label">
                                        {{ $currentImage ? 'Insert Banner Image Size (1440 X 650)' : 'Upload Banner Image' }}
                                    </label>
                                    <input type="file" name="image" class="form-control" id="imageInput"
                                        accept="image/*" {{ $currentImage ? '' : 'required' }}>
                                    @error('image')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-12 text-end">
                                    <button type="submit" class="btn btn-primary">{{ $currentImage ? 'Update' : 'Add' }}
                                        Banner</button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
