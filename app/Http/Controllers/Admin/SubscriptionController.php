<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with('customer')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->get();
        return view('admin.subscriptions.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'plan_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'auto_renew' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['subscription_code'] = 'SUB' . strtoupper(Str::random(8));
        $data['end_date'] = \Carbon\Carbon::parse($request->start_date)->addDays($request->duration_days);
        $data['auto_renew'] = $request->has('auto_renew');

        Subscription::create($data);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription created successfully.');
    }

    public function show(Subscription $subscription)
    {
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function edit(Subscription $subscription)
    {
        $customers = Customer::where('is_active', true)->get();
        return view('admin.subscriptions.edit', compact('subscription', 'customers'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'plan_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'auto_renew' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['end_date'] = \Carbon\Carbon::parse($request->start_date)->addDays($request->duration_days);
        $data['auto_renew'] = $request->has('auto_renew');

        $subscription->update($data);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription updated successfully.');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription deleted successfully.');
    }

    public function toggleStatus(Subscription $subscription)
    {
        $newStatus = $subscription->status === 'active' ? 'cancelled' : 'active';
        $subscription->update(['status' => $newStatus]);
        
        return response()->json([
            'success' => true,
            'message' => 'Subscription status updated successfully.',
            'status' => $subscription->status
        ]);
    }

    public function list()
    {
        $subscriptions = Subscription::with('customer')
            ->where('status', 'active')
            ->orderBy('end_date')
            ->paginate(15);
        return view('admin.subscriptions.list', compact('subscriptions'));
    }
}
