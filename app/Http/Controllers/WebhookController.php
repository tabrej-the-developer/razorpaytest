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

class WebhookController extends Controller
{


   public function handle(Request $request)
{
    $payload = $request->getContent();
    $signature = $request->header('X-Razorpay-Signature');

    try {
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
        $api->utility->verifyPaymentSignature([
            'razorpay_payment_id' => $data['payload']['payment']['entity']['id'] ?? null,
            'razorpay_order_id' => $data['payload']['order']['entity']['id'] ?? null,
            'razorpay_signature' => $signature
        ]);
    } catch (SignatureVerificationError $e) {
        return response()->json(['error' => 'Invalid signature'], 400);
    }

    $data = json_decode($payload, true);
    $event = $data['event'];

    if ($event === 'subscription.activated') {
        // activate course
    }

    if ($event === 'invoice.paid') {
        // ₹4 auto-debit success
    }

    if ($event === 'invoice.payment_failed') {
        // suspend / notify
    }

    return response()->json(['ok' => true]);
}

}
