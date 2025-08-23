@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Website Banners Management</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Add Banner Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Home Page</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">Add Home Banner</div>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.website-banners.create') }}?page_name=home" class="btn btn-primary btn-circle">
                                <i class="fas fa-plus">add</i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">About Page</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">Add About Banner</div>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.website-banners.create') }}?page_name=about" class="btn btn-success btn-circle">
                                <i class="fas fa-plus">add</i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Contact Page</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">Add Contact Banner</div>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.website-banners.create') }}?page_name=contact" class="btn btn-info btn-circle">
                                <i class="fas fa-plus">add</i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Services Page</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">Add Services Banner</div>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.website-banners.create') }}?page_name=services" class="btn btn-warning btn-circle">
                                <i class="fas fa-plus">add</i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Banners Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Banners</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" 
                     aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Filter by Page:</div>
                    <a class="dropdown-item" href="?page=home">Home Page</a>
                    <a class="dropdown-item" href="?page=about">About Page</a>
                    <a class="dropdown-item" href="?page=contact">Contact Page</a>
                    <a class="dropdown-item" href="?page=services">Services Page</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ request()->url() }}">Show All</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Page Name</th>
                            <th>Title</th>
                            <th>Subtitle</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $key => $banner)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                {{ $banner->page_name }}
                                <span class="badge badge-pill
                                    @if($banner->page_name == 'home') badge-primary
                                    @elseif($banner->page_name == 'about') badge-success
                                    @elseif($banner->page_name == 'contact') badge-info
                                    @elseif($banner->page_name == 'services') badge-warning
                                    @else badge-secondary
                                    @endif">
                                    {{ ucfirst($banner->page_name ?? 'N/A') }}
                                </span>
                            </td>
                            <td>{{ $banner->title ?? 'N/A' }}</td>
                            <td>{{ $banner->subtitle ?? 'N/A' }}</td>
                            <td>
                                @if($banner->image)
                                <img src="{{ asset('storage/'.$banner->image) }}" 
                                     class="img-thumbnail" width="120" 
                                     data-toggle="modal" data-target="#imageModal{{ $banner->id }}"
                                     style="cursor: pointer;">
                                
                                <!-- Image Modal -->
                                <div class="modal fade" id="imageModal{{ $banner->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Banner Preview - {{ ucfirst($banner->page_name) }} Page</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ asset('storage/'.$banner->image) }}" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.website-banners.edit', $banner->id) }}" 
                                       class="btn btn-primary btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.website-banners.destroy', $banner->id) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-image fa-3x text-gray-300 mb-3"></i>
                                <h5 class="text-gray-500">No banners found</h5>
                                <p class="text-gray-400">Click on the buttons above to add your first banner</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($banners->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-sm text-gray-700">
                    Showing {{ $banners->firstItem() }} to {{ $banners->lastItem() }} of {{ $banners->total() }} entries
                </div>
                <nav>
                    {{ $banners->links() }}
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-circle {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card:hover {
        transform: translateY(-5px);
        transition: transform 0.3s;
    }
    .img-thumbnail:hover {
        opacity: 0.8;
    }
    .alert {
        border-left: 4px solid;
    }
</style>
@endpush

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete confirmation with SweetAlert
        const deleteForms = document.querySelectorAll('.delete-form');
        
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
@endpush