<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class OfferController extends Controller
{
    // ✅ Get All Offers
    // public function getOffers()
    // {
    //     $offers = Offer::select('id', 'offer', 'created_at', 'updated_at')
    //         ->orderBy('id', 'desc')
    //         ->get();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Offers fetched successfully',
    //         'data' => $offers
    //     ], 200);
    // }
    public function getOffers()
    {
        $offers = Offer::select('id', 'offer', 'created_at', 'updated_at')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Offers fetched successfully',
            'data' => $offers
        ], 200);
    }



    public function createOffer(Request $request)
    {
        // ✅ Validation
        $request->validate([
            'offer' => 'required|string|max:255',
        ]);

        // ✅ Insert Offer
        $offer = Offer::create([
            'offer' => $request->offer,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Offer created successfully',
            'data' => $offer
        ], 201);
    }


    // public function updateOffer(Request $request, $id)
    // {
    //     // ✅ Validation
    //     $request->validate([
    //         'offer' => 'required|string|max:255',
    //     ]);

    //     // ✅ Find Offer
    //     $offer = Offer::find($id);

    //     if (!$offer) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Offer not found'
    //         ], 404);
    //     }

    //     // ✅ Update Offer
    //     $offer->update([
    //         'offer' => $request->offer
    //     ]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Offer updated successfully',
    //         'data' => $offer
    //     ], 200);
    // }




    public function updateOffer(Request $request, $id)
    {
        // ✅ Validation
        $validator = Validator::make($request->all(), [
            'offer' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // ✅ Find Offer
        $offer = Offer::find($id);

        if (!$offer) {
            return response()->json([
                'status' => false,
                'message' => 'Offer not found'
            ], 404);
        }

        // ✅ Update
        $offer->update([
            'offer' => $request->offer
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Offer updated successfully',
            'data' => $offer
        ], 200);
    }


    public function deleteOffer($id)
    {
        $offer = offer::find($id);
        if (!$offer) {
            return response()->json([
                'status' => false,
                'message' => 'Offer not found'
            ], 404);
        }
        $offer->delete();
        return response()->json([
            'status' => true,
            'message' => 'Offer deleted successfully'
        ], 200);
    }

    public function latestOffer()
    {
        $offer = Offer::latest()->first();

        if (!$offer) {
            return response()->json([
                'status' => false,
                'message' => 'No offers available'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Latest offer fetched successfully',
            'data' => $offer
        ], 200);
    }










}
