@extends('admin.layouts.app')

@section('title', 'Contact Enquiry Details - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Contact Enquiry #{{ $contact->id }}</h4>
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-secondary">
                    <i class="ri-arrow-left-line"></i> Back
                </a>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">Name</th>
                        <td>{{ $contact->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $contact->email }}</td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $contact->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Subject</th>
                        <td>{{ $contact->subject ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Message</th>
                        <td style="white-space: pre-line;">{{ $contact->message ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
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
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>
                            {{ $contact->replied_at ? $contact->replied_at->format('M d, Y H:i') : '-' }}
                        </td>
                    </tr>
                    @if($contact->read_at)
                    <tr>
                        <th>Read At</th>
                        <td>{{ $contact->read_at?->format('M d, Y H:i') ?? 'N/A' }}</td>
                    </tr>
                    @endif
                    @if($contact->replied_at)
                    <tr>
                        <th>Replied At</th>
                        <td>{{ $contact->replied_at?->format('M d, Y H:i') ?? 'N/A' }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

