@forelse($customers as $key=> $customer)
    <tr>
        <td>{{ $customer->$key + 1 }}</td>

        <td>
            <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-info" title="View">
                {{ $customer->name }}
            </a>
        </td>


        <td>{{ $customer->email }}</td>
        <td>{{ $customer->phone ?? 'N/A' }}</td>
        <td>
            <span class="badge bg-{{ $customer->is_active ? 'success' : 'danger' }} badge-sm">
                {{ $customer->is_active ? 'Active' : 'Inactive' }}
            </span>
        </td>
        <td>{{ $customer->created_at->format('M d, Y') }}</td>
        <td>
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-outline-warning btn-sm me-3"
                    title="Edit">
                    <i class="ri-edit-line"></i>
                </a>

                <button type="button" class="btn btn-outline-danger btn-sm delete-item" data-id="{{ $customer->id }}"
                    data-name="{{ $customer->name }}" data-type="customer"
                    data-url="{{ route('admin.customers.destroy', $customer->id) }}" title="Delete">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </td>

    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted">No customers found.</td>
    </tr>
@endforelse
