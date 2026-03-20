<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Webhook;
use App\Models\Payment;
use App\Models\order;

use Razorpay\Api\Api;
use App\Services\GatewayService;




class PaymentController extends Controller
{
    /**
     * Create a payment (Stripe charge)
     */
    public function pay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'token' => 'required|string', // token from frontend
            'order_id' => 'nullable|integer'
        ]);
        try {
            $stripeConfig = GatewayService::getConfig('stripe');
            Stripe::setApiKey($stripeConfig['secret']);

            $charge = Charge::create([
                'amount' => $request->amount * 100, // cents
                'currency' => 'usd',
                'source' => $request->token,
                'description' => 'Payment from Laravel API',
            ]);
            // Save payment in DB
            Payment::create([
                'order_id' => $request->order_id ?? null,
                'user_id' => auth()->id(),
                'transaction_id' => $charge->id,
                'payment_method' => 'stripe',
                'amount' => $request->amount,
                'status' => $charge->status,
                'response' => json_encode($charge),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Payment successful!',
                'data' => $charge
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
        $secret = GatewayService::getConfig('stripe')['secret'] ?? env('STRIPE_WEBHOOK_SECRET');

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




    // public function verifyRazorpayPayment(Request $request)
    // {
    //     $request->validate([
    //         'razorpay_payment_id' => 'required|string',
    //         'razorpay_order_id' => 'required|string',
    //         'razorpay_signature' => 'required|string',
    //     ]);

    //     try {
    //         $api = new Api(
    //             env('RAZORPAY_KEY_ID'),
    //             env('RAZORPAY_KEY_SECRET')
    //         );

    //         // 🔐 Signature verify
    //         $attributes = [
    //             'razorpay_order_id' => $request->razorpay_order_id,
    //             'razorpay_payment_id' => $request->razorpay_payment_id,
    //             'razorpay_signature' => $request->razorpay_signature,
    //         ];

    //         $api->utility->verifyPaymentSignature($attributes);

    //         // ✅ Payment DB me update karo
    //         $payment = Payment::where('transaction_id', $request->razorpay_payment_id)->first();

    //         if ($payment) {
    //             $payment->update([
    //                 'status' => 'paid',
    //             ]);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'verified' => true,
    //             'message' => 'Razorpay payment verified successfully'
    //         ], 200);

    //     } catch (\Exception $e) {

    //         // ❌ Signature fail ya kuch aur error
    //         return response()->json([
    //             'success' => false,
    //             'verified' => false,
    //             'message' => 'Payment verification failed',
    //             'error' => $e->getMessage()
    //         ], 400);
    //     }
    // }


    public function verifyRazorpayPayment(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        try {
            $api = GatewayService::getRazorpayApi();

            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // ✅ Step 1: Orders table se fetch
            $order = Order::where('razorpay_payment_id', $request->razorpay_payment_id)->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found for this payment ID',
                ], 404);
            }

            // ✅ Step 2: Payment table update/create
            $payment = Payment::where('transaction_id', $request->razorpay_payment_id)->first();
            if (!$payment) {
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'transaction_id' => $request->razorpay_payment_id,
                    'payment_method' => 'razorpay',
                    'amount' => $order->total_amount ?? 0,
                    'status' => 'paid',
                    'response' => json_encode($request->all()),
                ]);
            } else {
                $payment->update([
                    'status' => 'paid',
                ]);
            }

            // ✅ Step 3: Orders table status update
            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'confirmed',
            ]);

            return response()->json([
                'success' => true,
                'verified' => true,
                'message' => 'Razorpay payment verified successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'verified' => false,
                'message' => 'Payment verification failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }






}
