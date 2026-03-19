<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Pincodes;




class AddressController extends Controller
{

    // public function index()
    // {
    //     $userId = Auth::id(); // current logged in user
    //     $addresses = Address::where('user_id', $userId)->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $addresses
    //     ], 200);
    // }

    public function index()
    {
        // current user ID from token
        $userId = Auth::id();

        // get all addresses for this user
        $addresses = Address::where('user_id', $userId)->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ], 200);
    }

    // POST - add new address
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'house_no' => 'required|string|max:100',
    //         'apartment' => 'nullable|string|max:150',
    //         'area' => 'required|string|max:200',
    //         'save_as' => 'nullable|string|max:100',
    //         'receiver_name' => 'required|string|max:150',
    //         'receiver_phone' => 'required|string|max:20',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $address = Address::create([
    //         'user_id' => Auth::id(),
    //         'house_no' => $request->house_no,
    //         'apartment' => $request->apartment,
    //         'area' => $request->area,
    //         'save_as' => $request->save_as,
    //         'receiver_name' => $request->receiver_name,
    //         'receiver_phone' => $request->receiver_phone,
    //     ]);

    //     return response()->json(['message' => 'Address saved successfully', 'data' => $address], 201);
    // }



    // POST - store new address
    // public function store(Request $request)
    // {
    //     // Validation
    //     $validator = Validator::make($request->all(), [
    //         'full_name' => 'required|string|max:150',
    //         'phone' => 'required|string|max:20',
    //         'address_line' => 'required|string|max:255',
    //         'city' => 'required|string|max:100',
    //         'pincode' => 'required|string|max:20',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     // Create address
    //     $address = Address::create([
    //         'user_id' => Auth::id(),
    //         'full_name' => $request->full_name,
    //         'phone' => $request->phone,
    //         'address_line' => $request->address_line,
    //         'city' => $request->city,
    //         'pincode' => $request->pincode,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Address saved successfully',
    //         'data' => $address
    //     ], 201);
    // }


    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'address_line' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create address
        $address = Address::create($request->only([
            'full_name',
            'phone',
            'address_line',
            'city',
            'pincode'
        ]) + ['user_id' => Auth::id()]);

        return response()->json([
            'success' => true,
            'message' => 'Address saved successfully',
            'data' => $address
        ], 201);
    }







    // UPDATE - update user address
    // public function update(Request $request, $id)
    // {
    //     // Get address for current user
    //     $address = Address::where('id', $id)
    //         ->where('user_id', Auth::id())
    //         ->first();

    //     if (!$address) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Address not found'
    //         ], 404);
    //     }

    //     // Validation
    //     $validator = Validator::make($request->all(), [
    //         'full_name' => 'required|string|max:150',
    //         'phone' => 'required|string|max:20',
    //         'address_line' => 'required|string|max:255',
    //         'city' => 'required|string|max:100',
    //         'pincode' => 'required|string|max:20',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     // Update address
    //     $address->update([
    //         'full_name' => $request->full_name,
    //         'phone' => $request->phone,
    //         'address_line' => $request->address_line,
    //         'city' => $request->city,
    //         'pincode' => $request->pincode,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Address updated successfully',
    //         'data' => $address
    //     ], 200);
    // }




    public function update(Request $request, $id)
    {
        // Get address for current user
        $address = Address::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'address_line' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Use only fillable fields to update
        $address->update($request->only([
            'full_name',
            'phone',
            'address_line',
            'city',
            'pincode'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => $address
        ], 200);
    }

    public function Pincode()
    {
        try {
            $pincodes = Pincodes::where('status', 1)->get();

            return response()->json([
                'success' => true,
                'message' => 'Pincode list fetched successfully',
                'data' => $pincodes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }









}
