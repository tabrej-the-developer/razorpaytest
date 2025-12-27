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
use Razorpay\Api\Utility;

class WebhookController extends Controller
{


 public function handle(Request $request)
{
    $payload   = $request->getContent(); // raw body
    $signature = $request->header('X-Razorpay-Signature');
    $secret    = env('RAZORPAY_WEBHOOK_SECRET');

    // 1) Compute expected signature
    $expected = hash_hmac('sha256', $payload, $secret);

    // 2) Compare securely
    if (! hash_equals($expected, $signature)) {
        Log::error('Webhook signature failed', [
            'expected' => $expected,
            'got'      => $signature,
        ]);

        return response()->json(['error' => 'Invalid signature'], 400);
    }

    // 3) Parse and handle events
    $data  = json_decode($payload, true);
    $event = $data['event'] ?? null;

    if ($event === 'subscription.activated') {
        Log::info('Subscription activated', $data);
        // mark user subscription active
    }

    if ($event === 'invoice.paid') {
        Log::info('Invoice paid', $data);
        // ₹4 auto-debit success
    }

    if ($event === 'invoice.payment_failed') {
        Log::info('Invoice failed', $data);
        // notify + maybe suspend access
    }

    return response()->json(['ok' => true]);
}

}
