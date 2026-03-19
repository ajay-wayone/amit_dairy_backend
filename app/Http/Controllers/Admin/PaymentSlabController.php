<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSlab;
use Illuminate\Http\Request;

class PaymentSlabController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'min_km' => 'required|numeric',
            'max_km' => 'nullable|numeric',
            'advance_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'nullable|boolean',
        ]);

        // agar naya slab active ho raha hai
        if ($request->status == 1) {
            // sab slabs inactive kar do
            PaymentSlab::where('status', 1)->update(['status' => 0]);
        }

        PaymentSlab::create([
            'min_km' => $request->min_km,
            'max_km' => $request->max_km,
            'advance_percentage' => $request->advance_percentage,
            'status' => $request->status ?? 0,
        ]);

        return back()->with('success', 'Payment slab added successfully');
    }

    public function update(Request $request, PaymentSlab $slab)
    {
        $request->validate([
            'min_km' => 'required|numeric',
            'max_km' => 'nullable|numeric',
            'advance_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'nullable|boolean',
        ]);

        if ($request->status == 1) {
            PaymentSlab::where('status', 1)
                ->where('id', '!=', $slab->id)
                ->update(['status' => 0]);
        }

        $slab->update([
            'min_km' => $request->min_km,
            'max_km' => $request->max_km,
            'advance_percentage' => $request->advance_percentage,
            'status' => $request->status ?? 0,
        ]);

        return back()->with('success', 'Payment slab updated successfully');
    }

    public function destroy(PaymentSlab $slab)
    {
        $slab->delete();
        return back()->with('success', 'Payment slab deleted successfully');
    }
}
