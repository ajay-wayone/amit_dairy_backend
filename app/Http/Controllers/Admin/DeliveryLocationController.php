<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryLocation;
use Illuminate\Http\Request;

class DeliveryLocationController extends Controller
{
    public function index()
    {
        $deliveryLocations = DeliveryLocation::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.delivery-locations.index', compact('deliveryLocations'));
    }

    public function create()
    {
        return view('admin.delivery-locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'pincode' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
        ]);

        DeliveryLocation::create($request->all());

        return redirect()->route('admin.delivery-locations.index')
            ->with('success', 'Delivery location created successfully!');
    }

    public function show(DeliveryLocation $deliveryLocation)
    {
        return view('admin.delivery-locations.show', compact('deliveryLocation'));
    }

    public function edit(DeliveryLocation $deliveryLocation)
    {
        return view('admin.delivery-locations.edit', compact('deliveryLocation'));
    }

    public function update(Request $request, DeliveryLocation $deliveryLocation)
    {
        $request->validate([
            'location' => 'required|string',
            'pincode' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
        ]);

        $deliveryLocation->update($request->all());

        return redirect()->route('admin.delivery-locations.index')
            ->with('success', 'Delivery location updated successfully!');
    }

    public function destroy(DeliveryLocation $deliveryLocation)
    {
        $deliveryLocation->delete();

        return redirect()->route('admin.delivery-locations.index')
            ->with('success', 'Delivery location deleted successfully!');
    }

    public function toggleStatus(DeliveryLocation $deliveryLocation)
    {
        $deliveryLocation->update([
            'is_active' => !$deliveryLocation->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!',
            'is_active' => $deliveryLocation->is_active
        ]);
    }
}
