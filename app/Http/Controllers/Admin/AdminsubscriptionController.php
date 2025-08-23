<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adminsubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminsubscriptionController extends Controller
{
    public function index()
    {
        $subscription_admin_products = Adminsubscription::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.subscriptions.index', compact('subscription_admin_products'));
    }

    public function create()
    {
        return view('admin.subscriptions.create');
    }

    public function store(Request $request)
    {
        dd($request->all());

        $request->validate([
            'plan_name'   => 'required|string|max:255',
            'valid_days'  => 'required|integer|min:1',
            'amount'      => 'required|numeric|min:0',
            'status'      => 'required|boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
        ]);

        try {
            $data = $request->only(['plan_name', 'valid_days', 'amount', 'status', 'description']);

            // Image Upload
            if ($request->hasFile('image')) {
                $image         = $request->file('image');
                $filename      = 'subscription_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $data['image'] = $image->storeAs('subscriptions', $filename, 'public');
            }

            Adminsubscription::create($data);

            return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }

    public function edit(Adminsubscription $subscription)
    {
        return view('admin.subscriptions.edit', compact('subscription'));
    }

    public function update(Request $request, Adminsubscription $subscription)
    {
        $request->validate([
            'plan_name'   => 'required|string|max:255',
            'valid_days'  => 'required|integer|min:1',
            'amount'      => 'required|numeric|min:0',
            'status'      => 'required|boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
        ]);

        try {
            $data = $request->only(['plan_name', 'valid_days', 'amount', 'status', 'description']);

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($subscription->image && Storage::disk('public')->exists($subscription->image)) {
                    Storage::disk('public')->delete($subscription->image);
                }

                $image         = $request->file('image');
                $filename      = 'subscription_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $data['image'] = $image->storeAs('subscriptions', $filename, 'public');
            }

            $subscription->update($data);

            return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update subscription: ' . $e->getMessage());
        }
    }

    public function destroy(Adminsubscription $subscription)
    {
        try {
            if ($subscription->image && Storage::disk('public')->exists($subscription->image)) {
                Storage::disk('public')->delete($subscription->image);
            }

            $subscription->delete();

            return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete subscription: ' . $e->getMessage());
        }
    }
}
