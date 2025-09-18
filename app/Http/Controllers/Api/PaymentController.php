<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Webhook;
use App\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Create a payment (Stripe charge)
     */
    public function pay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'token'  => 'required|string', // token from frontend
            'order_id' => 'nullable|integer'
        ]);
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $charge = Charge::create([
                'amount'      => $request->amount * 100, // cents
                'currency'    => 'usd',
                'source'      => $request->token,
                'description' => 'Payment from Laravel API',
            ]);
            // Save payment in DB
            Payment::create([
                'order_id'       => $request->order_id ?? null,
                'user_id'        => auth()->id(),
                'transaction_id' => $charge->id,
                'payment_method' => 'stripe',
                'amount'         => $request->amount,
                'status'         => $charge->status,
                'response'       => json_encode($charge),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Payment successful!',
                'data'    => $charge
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Webhook for Stripe events
     */
    public function webhook(Request $request)
    {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $secret
            );

            if ($event->type === 'charge.succeeded') {
                $charge = $event->data->object;

                Payment::where('transaction_id', $charge->id)
                    ->update(['status' => 'succeeded']);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get logged-in user payments
     */
    public function index()
    {
        $payments = Payment::where('user_id', auth()->id())->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Show a single payment
     */
    public function show($id)
    {
        $payment = Payment::where('user_id', auth()->id())->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $payment
        ]);
    }
}
