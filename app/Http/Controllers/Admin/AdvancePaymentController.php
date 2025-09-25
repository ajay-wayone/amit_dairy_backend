<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use Illuminate\Http\Request;

class AdvancePaymentController extends Controller
{
    /**
     * Show Advance Payment list
     */
    public function index()
    {
        $records = AdvancePayment::all();
        return view('admin.payments.index', compact('records'));
    }

    /**
     * Store new Advance Payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'percentage' => 'required|integer|min:1|max:100',
        ]);

        AdvancePayment::create([
            'percentage' => $request->percentage,
        ]);

        return redirect()->back()->with('success', 'Advance payment added successfully.');
    }

    /**
     * Update Advance Payment
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'percentage' => 'required|integer|min:1|max:100',
        ]);

        $advance = AdvancePayment::findOrFail($id);
        $advance->update([
            'percentage' => $request->percentage,
        ]);

        return redirect()->back()->with('success', 'Advance payment updated successfully.');
    }
}
