<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;
use App\Models\PaymentSlab;

class OfferController extends Controller
{
    /**
     * Show list of all offers
     */
    public function index()
    {
        $offers = Offer::latest()->get();
        return view('admin.offer.index', compact('offers'));
    }

    /**
     * Show create form
     */


    public function create()
    {
        $offers = Offer::latest()->get();
        $slabs = PaymentSlab::orderBy('min_km')->get();

        return view('admin.offer.create', compact('offers', 'slabs'));
    }


    /**
     * Store new offer
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'offer' => 'required|string|max:255|unique:offers,offer',
        ]);

        Offer::create([
            'offer' => $request->offer,
        ]);

        return back()->with('success', 'Offer added successfully!');
    }

    /**
     * Update existing offer
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'offer' => "required|string|max:255|unique:offers,offer,$id",
        ]);

        $offer = Offer::findOrFail($id);
        $offer->offer = $request->offer;
        $offer->save();

        return back()->with('success', 'Offer updated successfully!');
    }

    /**
     * Delete offer
     */
    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        $offer->delete();

        return back()->with('success', 'Offer deleted successfully!');
    }




















}
