@extends('admin.layouts.app')

@php
    $selectedPage = $selectedPage ?? null;
    $currentImage = $currentImage ?? null;
@endphp

@section('title', 'Manage Banner')

@section('content')
    <div class="row">
        @if (session('success'))
        @elseif(session('error'))
        @endif

        <div class="col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Manage Banner</h4>
                </div>

                <div class="card-body">

                    <!-- PAGE SELECTION FORM -->
                    <form method="GET" action="{{ route('admin.banners.create') }}">
                        <div class="mb-3">
                            <label for="pageSelect" class="form-label">Select Page</label>
                            <select name="page_name" id="pageSelect" class="form-select" onchange="this.form.submit()"
                                required>
                                <option value="">-- Select Page --</option>
                                <option value="home" {{ request('page_name') == 'home' ? 'selected' : '' }}>Home</option>
                                <option value="about" {{ request('page_name') == 'about' ? 'selected' : '' }}>About
                                </option>
                                <option value="product" {{ request('page_name') == 'product' ? 'selected' : '' }}>Products
                                </option>
                                <option value="contact" {{ request('page_name') == 'contact' ? 'selected' : '' }}>Contact
                                </option>
                            </select>
                        </div>
                    </form>

                    <!-- IMAGE UPLOAD FORM: SHOWN ONLY IF PAGE IS SELECTED -->
                    @if ($selectedPage)
                        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="submit_banner" value="1">
                            <input type="hidden" name="page_name" value="{{ $selectedPage }}">

                            <div class="row">
                                @if ($currentImage)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Current Banner</label><br>
                                        <img src="{{ asset($currentImage) }}" alt="Current Banner"
                                            style="max-width: 100%; height: auto; border:1px solid #ccc;">
                                    </div>
                                @endif

                                <div class="col-md-6 mb-3">
                                    <label for="imageInput" class="form-label">
                                        {{ $currentImage ? 'Update Banner Image (1440x650)' : 'Upload Banner Image' }}
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
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
