@extends('admin.layouts.app')

@section('title', 'Payment Slabs')

@section('content')
<div class="container mt-4">

    <h4>Add Payment Slab</h4>

    <form action="{{ route('admin.slabs.store') }}" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col">
                <input type="number" name="min_km" class="form-control" placeholder="Min KM" required>
            </div>
            <div class="col">
                <input type="number" name="max_km" class="form-control" placeholder="Max KM">
            </div>
            <div class="col">
                <input type="number" name="advance_percentage" class="form-control" placeholder="Advance %" required>
            </div>


            <div class="col-md-2 d-flex align-items-center">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="status" value="1">
        <label class="form-check-label">Active</label>
    </div>
</div>

            <div class="col">
                <button class="btn btn-primary">Add Slab</button>
            </div>
        </div>
    </form>

    <h4 class="mt-4">Slabs List</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Min KM</th>
                <th>Max KM</th>
                <th>Advance %</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($slabs as $slab)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $slab->min_km }}</td>
                <td>{{ $slab->max_km }}</td>
                <td>{{ $slab->advance_percentage }}%</td>
                <td>
                    <form action="{{ route('admin.slabs.delete', $slab->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
