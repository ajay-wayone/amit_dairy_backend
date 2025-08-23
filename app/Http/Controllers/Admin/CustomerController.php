<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'DESC')->paginate(10);

        if ($request->ajax()) {
            $table      = view('admin.customers.partials.table', compact('users'))->render();
            $pagination = view('admin.customers.partials.pagination', compact('users'))->render();

            return response()->json([
                'table'      => $table,
                'pagination' => $pagination,
            ]);
        }

        return view('admin.customers.index', compact('users'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('full_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'full_name', 'email', 'phone']);

        return response()->json($users);
    }

    public function destroy(User $user)
    {
        // Agar User ke orders ho to delete na karein
        if ($user->orders()->count() > 0) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Cannot delete user with existing orders.');
        }

        $user->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => ! $user->is_active]);

        return response()->json([
            'success'   => true,
            'message'   => 'User status updated successfully.',
            'is_active' => $user->is_active,
        ]);
    }
}
