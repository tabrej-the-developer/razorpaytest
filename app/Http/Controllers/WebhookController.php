<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UserSubscription;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');
        $secret = env('RAZORPAY_WEBHOOK_SECRET');

        // Verify webhook signature [conversation_history:38][conversation_history:39]
        $expected = hash_hmac('sha256', $payload, $secret);
        if (!hash_equals($expected, $signature)) {
            Log::error('Webhook signature failed');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? null;

        Log::info('Webhook received', ['event' => $event]);

        match($event) {
            'subscription.activated' => $this->handleActivated($data),
            'invoice.paid' => $this->handleInvoicePaid($data),
            'subscription.charged' => $this->handleCharged($data),
            'invoice.payment_failed' => $this->handlePaymentFailed($data),
            default => Log::info('Unhandled event: ' . $event)
        };

        return response()->json(['ok' => true], 200);
    }

    private function handleActivated($data)
    {
        $subscriptionId = $data['payload']['subscription']['entity']['id'];
        UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->update(['status' => 'active']);
    }

    private function handleInvoicePaid($data)
    {
        $subscriptionId = $data['payload']['subscription']['entity']['id'];
        UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->update(['status' => 'active']);
        // Extend course access here
    }

    private function handleCharged($data)
    {
        $subscriptionId = $data['payload']['subscription']['entity']['id'];
        UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->update(['status' => 'active']);
    }

    private function handlePaymentFailed($data)
    {
        $subscriptionId = $data['payload']['subscription']['entity']['id'];
        UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->update(['status' => 'payment_failed']);
        // Send notification to user
    }
}
