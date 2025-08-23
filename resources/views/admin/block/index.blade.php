@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Heading -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-3">
                    <h5 class="m-0 font-weight-bold"><i class="fas fa-clock"></i> Manage Time and Date</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Slot Form -->
    <div class="card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Add Blocked Time Slot</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('block.store') }}" method="POST" id="slotForm">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date (optional)</label>
                        <input type="date" name="blocked_date" class="form-control">
                        <small class="form-text text-muted">Leave empty to block this time for all dates</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Time Slot</label>
                        <select class="form-control" name="time_select" id="timeSelect">
                            <option value="">-- Select Time Slot --</option>
                            <option value="10:00-18:00">10 AM - 6 PM</option>
                            <option value="11:00-21:00">11 AM - 9 PM</option>
                        </select>
                    </div>
                </div>

                <!-- Custom Time Slots -->
                <div class="row d-none" id="customTimeDiv">
                    <div id="customSlotsContainer" class="w-100">
                        <div class="row mb-2 custom-slot">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="custom_start_time[]" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Time</label>
                                <input type="time" name="custom_end_time[]" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <button type="button" id="addCustomSlotBtn" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add Another Custom Slot
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-danger mt-3">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Slots Table -->
    <div class="card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Blocked Time Slots</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-0">Showing {{ count($blockedSlots) }} blocked time slots</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="search-container ms-auto">
                        <div class="input-group">
                            <input type="text" id="slotSearchInput" class="form-control" placeholder="Search date or time...">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="slotsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blockedSlots as $index => $slot)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $slot->blocked_date ?? 'NA' }}</td>
                            <td>
                                @if($slot->start_time && $slot->end_time)
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('block.destroy', $slot->id) }}" method="POST" class="delete-slot-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">No blocked time slots found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const timeSelect = document.getElementById('timeSelect');
    const customDiv = document.getElementById('customTimeDiv');
    const customContainer = document.getElementById('customSlotsContainer');
    const addBtn = document.getElementById('addCustomSlotBtn');

    // Toggle custom time input
    timeSelect.addEventListener('change', function () {
        if(this.value === 'custom'){
            customDiv.classList.remove('d-none');
        } else {
            customDiv.classList.add('d-none');
            // Reset slots
            customContainer.innerHTML = `
                <div class="row mb-2 custom-slot">
                    <div class="col-md-6 mb-3">
                        <input type="time" name="custom_start_time[]" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <input type="time" name="custom_end_time[]" class="form-control">
                    </div>
                </div>
            `;
        }
    });

    // Add new custom slot
    addBtn.addEventListener('click', function() {
        const newSlot = document.createElement('div');
        newSlot.classList.add('row', 'mb-2', 'custom-slot');
        newSlot.innerHTML = `
            <div class="col-md-6 mb-3">
                <input type="time" name="custom_start_time[]" class="form-control" placeholder="Start Time">
            </div>
            <div class="col-md-6 mb-3">
                <input type="time" name="custom_end_time[]" class="form-control" placeholder="End Time">
            </div>
        `;
        customContainer.appendChild(newSlot);
    });

    // SweetAlert Delete
    document.querySelectorAll('.delete-slot-form').forEach(form => {
        form.addEventListener('submit', function(e){
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the time slot.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if(result.isConfirmed) form.submit();
            });
        });
    });
});
</script>
@endsection
