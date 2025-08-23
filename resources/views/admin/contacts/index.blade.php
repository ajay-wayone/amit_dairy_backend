@extends('admin.layouts.app')

@section('title', 'Contact Enquiries - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Contact Enquiries</h4>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.contacts.index') }}" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search enquiries..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                </div>

                <!-- Contacts Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contacts as $contact)
                            <tr>
                                <td>{{ $contact->id }}</td>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->phone ?? 'N/A' }}</td>
                                <td>{{ Str::limit($contact->subject ?? 'N/A', 30) }}</td>
                                <td>{{ Str::limit($contact->message ?? 'N/A', 50) }}</td>
                                <td>
                        @if($contact->status === 'new')
                            <span class="badge bg-warning">Unread</span>
                        @elseif($contact->status === 'read')
                            <span class="badge bg-success">Read</span>
                        @elseif($contact->status === 'replied')
                            <span class="badge bg-info">Replied</span>
                        @elseif($contact->status === 'closed')
                            <span class="badge bg-danger">Closed</span>
                        @endif
                    </td>
                                <td>{{ $contact->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.contacts.show', $contact->id) }}" class="btn btn-sm btn-info">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-item" 
                                                data-id="{{ $contact->id }}" 
                                                data-name="{{ $contact->name }}"
                                                data-type="contact enquiry"
                                                data-url="{{ route('admin.contacts.destroy', $contact->id) }}">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No contact enquiries found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($contacts->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $contacts->firstItem() }} to {{ $contacts->lastItem() }} of {{ $contacts->total() }} entries
                    </div>
                    <div class="pagination-container">
                        {{ $contacts->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 