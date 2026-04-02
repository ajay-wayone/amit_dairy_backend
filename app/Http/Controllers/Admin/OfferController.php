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
        $request->validate([
            'offer' => 'required|string|max:255',
            'coupon_code' => 'required|string|max:50|unique:offers,coupon_code',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'max_discount' => 'required|numeric|min:0',
        ]);

        Offer::create([
            'offer' => $request->offer,
            'coupon_code' => $request->coupon_code,
            'discount_percentage' => $request->discount_percentage,
            'max_discount' => $request->max_discount,
            'status' => $request->has('status') ? 1 : 0,
        ]);

        return back()->with('success', 'Coupon created successfully!');
    }


    /**
     * Update existing offer
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'offer' => 'required|string|max:255',
            'coupon_code' => "required|string|max:50|unique:offers,coupon_code,$id",
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'max_discount' => 'required|numeric|min:0',
        ]);

        $offer = Offer::findOrFail($id);
        $offer->update([
            'offer' => $request->offer,
            'coupon_code' => $request->coupon_code,
            'discount_percentage' => $request->discount_percentage,
            'max_discount' => $request->max_discount,
            'status' => $request->has('status') ? 1 : 0,
        ]);

        return back()->with('success', 'Coupon updated successfully!');
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
