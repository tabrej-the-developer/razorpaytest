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
        $event = $request->event;

        if ($event === 'subscription.activated') {
            // enable course fully
        }

        if ($event === 'invoice.paid') {
            // ₹4 auto-debit success
        }

        if ($event === 'invoice.payment_failed') {
            // suspend features / notify
        }

        return response()->json(['ok' => true]);
    }
}
