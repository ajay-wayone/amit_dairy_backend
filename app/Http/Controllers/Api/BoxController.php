<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Box;
use Illuminate\Http\Request;

class BoxController extends Controller
{
    // Get all active boxes
  public function index(Request $request)
{
    $boxes = Box::where('is_active', true)
        ->orderBy('box_name')
        ->get(); // <- get() use kiya

    return response()->json([
        'success' => true,
        'data'    => $boxes
    ]);
}


    // Get single box by id
    public function show($id)
    {
        $box = Box::where('is_active', true)->find($id);

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'Box not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $box
        ]);
    }
}
