@forelse($users as $key => $user)
    <tr>
        <td>{{ $key + 1 }}</td>

        <td>

            {{ $user->full_name }}

        </td>

        <td>{{ $user->email }}</td>
        <td>{{ $user->phone ?? 'N/A' }}</td>
        <td>
            @php
                $status = 'Active'; // default
                $badge = 'success'; // default green

                if (isset($user->is_active)) {
                    if ($user->is_active == 1) {
                        $status = 'Active';
                        $badge = 'success';
                    } elseif ($user->is_active == 0) {
                        $status = 'Inactive';
                        $badge = 'danger';
                    }
                }
            @endphp

            <span class="badge bg-{{ $badge }} badge-sm">
                {{ $status }}
            </span>

        </td>
        <td>{{ $user->created_at->format('M d, Y') }}</td>
        <td>
            <div class="d-flex justify-content-center btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-danger btn-sm delete-item" data-id="{{ $user->id }}"
                    data-name="{{ $user->full_name }}" data-type="user"
                    data-url="{{ route('admin.customers.destroy', $user->id) }}" title="Delete">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>

        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted">No users found.</td>
    </tr>
@endforelse
