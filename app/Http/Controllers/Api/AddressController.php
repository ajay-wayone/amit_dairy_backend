<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    // GET - fetch all addresses of logged in user
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())->get();
        return response()->json(['data' => $addresses], 200);
    }

    // POST - add new address
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'house_no' => 'required|string|max:100',
            'apartment' => 'nullable|string|max:150',
            'area' => 'required|string|max:200',
            'save_as' => 'nullable|string|max:100',
            'receiver_name' => 'required|string|max:150',
            'receiver_phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $address = Address::create([
            'user_id' => Auth::id(),
            'house_no' => $request->house_no,
            'apartment' => $request->apartment,
            'area' => $request->area,
            'save_as' => $request->save_as,
            'receiver_name' => $request->receiver_name,
            'receiver_phone' => $request->receiver_phone,
        ]);

        return response()->json(['message' => 'Address saved successfully', 'data' => $address], 201);
    }

    // UPDATE - update user address
    public function update(Request $request, $id)
    {
        $address = Address::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'house_no' => 'required|string|max:100',
            'apartment' => 'nullable|string|max:150',
            'area' => 'required|string|max:200',
            'save_as' => 'nullable|string|max:100',
            'receiver_name' => 'required|string|max:150',
            'receiver_phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $address->update($request->all());

        return response()->json(['message' => 'Address updated successfully', 'data' => $address], 200);
    }
}
