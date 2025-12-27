<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Razorpay\Api\Payment;
use Razorpay\Api\Order;
use Razorpay\Api\Subscription;
use App\Models\Order as OrderModel;
use App\Models\Subscription as SubscriptionModel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    // ₹4 Subscription (UPI AutoPay)
     public function createSubscription(Request $request)
    {
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        $subscription = $api->subscription->create([
            'plan_id' => 'plan_RvPX8a8DdPRmx9',
            'customer_notify' => 1,
            'total_count' => 1,
            'notes' => [
                'user_id' => Auth::user()->id
            ]
        ]);

        return response()->json([
            'subscription_id' => $subscription->id
        ]);
    }


    public function createSixRupeeOrder()
    {
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        $order = $api->order->create([
            'amount'   => 600,   // 6 rupees
            'currency' => 'INR',
            'receipt'  => uniqid()
        ]);

        return response()->json(['order_id' => $order->id]);
    }


    public function verifySixRupeePayment(Request $request)
    {
        $signature = hash_hmac(
            'sha256',
            $request->razorpay_order_id . '|' . $request->razorpay_payment_id,
            config('services.razorpay.secret')
        );

        if ($signature !== $request->razorpay_signature) {
            return response()->json(['status' => 'failed'], 400);
        }

        // SAVE: ₹6 paid
        // mark: user ready for subscription

        return response()->json(['status' => 'success']);
    }




    // Checkout page
    public function checkout()
    {
        return view('pay6');
    }
}
