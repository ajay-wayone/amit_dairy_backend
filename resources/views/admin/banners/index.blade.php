@extends('admin.layouts.app')

@php
    $selectedPage = $selectedPage ?? null;
    $currentImage = $currentImage ?? null;
@endphp

@section('title', 'Manage Banners')

@section('content')
    <div class="row">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="col-xxl-12">
            <div class="card">

                <!-- Header with Add Button -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Manage Banners</h4>
                <div class="d-flex gap-1">
                    <a href="{{ route('admin.banners.create') }}" class="btn btn-sm btn-primary">
                        <i class="ri-add-line"></i> Add Apps Banner
                    </a>
                 <a href="{{ route('admin.website-banners.index') }}" class="btn btn-sm btn-primary">
                <i class="ri-add-line"></i> Add Website Banner
            </a>



                </div>


                </div>

                <div class="card-body">
                    <!-- PAGE SELECTION FORM -->
                    

                    <!-- IMAGE UPLOAD FORM -->
                    @if ($selectedPage)
                        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="mb-5">
                            @csrf
                            <input type="hidden" name="submit_banner" value="1">
                            <input type="hidden" name="page_name" value="{{ $selectedPage }}">

                            <div class="row">
                                @if ($currentImage)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Current Banner</label><br>
                                        <img src="{{ asset($currentImage) }}" alt="Current Banner"
                                            style="max-width: 100%; height: auto; border:1px solid #ccc; border-radius: 8px;">
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
                                    <button type="submit" class="btn btn-primary">
                                        {{ $currentImage ? 'Update' : 'Add' }} Banner
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif

                    <!-- BANNERS LIST TABLE -->
                    <h5 class="mb-3">All Banners</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sr.n</th>
                                    <th>Banner</th>
                                    <th>Status</th>
                                    <th width="180">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($banners as $key => $banner)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                       <td>
                                    @if ($banner->image)
                                        <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner"
                                            style="height: 60px; border-radius: 6px;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>

                                        <td>
                                            <span class="badge {{ $banner->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $banner->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    <td>
    <!-- Edit Button -->
    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-sm btn-info" title="Edit">
        <i class="ri-edit-2-line"></i>
    </a>

    <!-- Delete Button -->
    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline-block"
          onsubmit="return confirm('Are you sure you want to delete this banner?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
            <i class="ri-delete-bin-line"></i>
        </button>
    </form>
</td>


                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No banners available</td>
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
