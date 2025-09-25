<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;

class AdvancePaymentController extends Controller
{
    /**
     * Get all Advance Payments (API)
     */
    public function index()
    {
        $records = AdvancePayment::all();

        return response()->json([
            'success' => true,
            'data' => $records
        ]);
    }
}
