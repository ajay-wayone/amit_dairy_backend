<?php
namespace App\Http\Controllers\Admin;
use App\Models\Usersubscriptions;
use App\Http\Controllers\Controller;
use App\Models\subscription;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class subscriptionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $subscriptions = Subscription::when($search, function ($query, $search) {
            return $query->where('title', 'like', "%{$search}%");
        })
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // To preserve the search query in pagination links
        $subscriptions->appends(['search' => $search]);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }
    public function list()
    {
        $subscriptions = Usersubscriptions::orderBy('created_at', 'desc')->get();
        return view('admin.subscriptions.list', compact('subscriptions'));
    }


    public function create()
    {
        return view('admin.subscriptions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'valid_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20248',
            'description' => 'nullable|string',
        ]);

        try {
            $data = $request->only([
                'title',
                'valid_days',
                'price',
                'status',
                'description'
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = 'subscription_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $data['image'] = $image->storeAs('subscriptions', $filename, 'public');
            }

            Subscription::create($data);

            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Subscription created successfully!');
        }                                       
        catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }


    public function edit(subscription $subscription)
    {
        return view('admin.subscriptions.edit', compact('subscription'));
    }
    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'valid_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20248',
            'description' => 'nullable|string',
        ]);

        try {
            // Prepare data
            $data = $request->only(['title', 'valid_days', 'price', 'status', 'description']);

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($subscription->image && Storage::disk('public')->exists($subscription->image)) {
                    Storage::disk('public')->delete($subscription->image);
                }

                // Store new image with unique name
                $image = $request->file('image');
                $filename = 'subscription_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $data['image'] = $image->storeAs('subscriptions', $filename, 'public');
            }

            // Update subscription
            $subscription->update($data);

            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Subscription updated successfully!');
        }
        catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update subscription: ' . $e->getMessage());
        }
    }


    public function destroy(subscription $subscription)
    {
        try {
            if ($subscription->image && Storage::disk('public')->exists($subscription->image)) {
                Storage::disk('public')->delete($subscription->image);
            }

            $subscription->delete();

            return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription deleted successfully!');
        }
        catch (\Exception $e) {
            return back()->with('error', 'Failed to delete subscription: ' . $e->getMessage());
        }
    }
}
