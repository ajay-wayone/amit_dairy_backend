@extends('admin.layouts.app')

@section('title', 'Newsletter Subscriptions - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Newsletter Subscriptions</h4>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.newsletters.index') }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2"
                                    placeholder="Search subscriptions..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </form>
                        </div>
                    </div>

                    <!-- Newsletter Subscriptions Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sr.n</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Subscribed At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($newsletters as $key => $newsletter)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $newsletter->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $newsletter->is_active ? 'success' : 'danger' }}">
                                                {{ $newsletter->is_active ? 'Active' : 'Unsubscribed' }}
                                            </span>
                                        </td>
                                        <td>{{ $newsletter->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-danger delete-item"
                                                    data-id="{{ $newsletter->id }}" data-name="{{ $newsletter->email }}"
                                                    data-type="newsletter subscription"
                                                    data-url="{{ route('admin.newsletters.destroy', $newsletter->id) }}">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No newsletter subscriptions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($newsletters->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $newsletters->firstItem() }} to {{ $newsletters->lastItem() }} of
                                {{ $newsletters->total() }} entries
                            </div>
                            <div class="pagination-container">
                                {{ $newsletters->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
