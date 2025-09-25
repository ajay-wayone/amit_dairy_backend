@extends('admin.layouts.app')

@section('title', 'Manage Advance Payment - Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">

            {{-- Add Advance Payment Form --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Add Advance Payment</h4>
                </div>
                <div class="card-body">
                    <!-- <form method="POST" action="{{ route('admin.payments.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="percentage" class="form-label small">Advance Payment (%) *</label>
                            <input type="number" name="percentage" id="percentage"
                                class="form-control form-control-sm @error('percentage') is-invalid @enderror"
                                placeholder="Enter percentage e.g. 20" value="{{ old('percentage') }}" min="1" max="100"
                                required>
                            @error('percentage')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-secondary btn-sm">Reset</button>
                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                        </div>
                    </form> -->
                </div>
            </div>

            {{-- Advance Payment List --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Advance Payment List</h4>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Advance Payment (%)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $record)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $record->percentage }}%</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning editBtn" data-id="{{ $record->id }}"
                                            data-percentage="{{ $record->percentage }}" data-bs-toggle="modal"
                                            data-bs-target="#editModal">Edit</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No Records Found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Advance Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="edit_percentage" class="form-label small">Advance Payment (%) *</label>
                            <input type="number" name="percentage" id="edit_percentage" class="form-control form-control-sm"
                                min="1" max="100" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Edit Modal Handler
        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const action = "{{ url('admin/payments') }}/" + id;

                document.getElementById('editForm').action = action;
                document.getElementById('edit_percentage').value = this.dataset.percentage;
            });
        });
    </script>
@endpush