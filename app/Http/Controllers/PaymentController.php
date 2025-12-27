<?php

namespace App\Http\Controllers;

use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class PaymentController extends Controller
{
    private $api;

    public function __construct()
    {
        $this->api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    }

    // Step 1: Create Subscription (₹6 upfront + ₹4 mandate)
   public function createSubscription(Request $request)
{
    Log::info('🔔 createSubscription API called');

    // 🔴 AUTH DEBUG
    $user = Auth::user();

    if (!$user) {
        Log::error('❌ User not authenticated');
        return response()->json([
            'error' => 'Unauthenticated'
        ], 401);
    }

    Log::info('👤 User authenticated', [
        'user_id' => $user->id,
        'email' => $user->email ?? null,
    ]);

    // 🔴 SUBSCRIPTION CHECK DEBUG
    $alreadySubscribed = UserSubscription::where('user_id', $user->id)
        ->where('status', 'active')
        ->exists();

    Log::info('📦 Active subscription exists?', [
        'exists' => $alreadySubscribed
    ]);

    if ($alreadySubscribed) {
        return response()->json([
            'error' => 'Already subscribed'
        ], 400);
    }

    try {
        // 🔴 RAZORPAY PAYLOAD DEBUG
        $payload = [
            'plan_id' => env('RAZORPAY_PLAN_ID'),
            'total_count' => 12, // REQUIRED
            'quantity' => 1,
            'customer_notify' => 1,
            'notes' => [
                'user_id' => $user->id,
                'course' => 'HRMS Course'
            ],
        ];

        Log::info('📤 Creating Razorpay subscription', $payload);

        $subscription = $this->api->subscription->create($payload);

        Log::info('✅ Razorpay subscription created', [
            'subscription_id' => $subscription->id,
            'status' => $subscription->status ?? null
        ]);

        return response()->json([
            'subscription_id' => $subscription->id,
            'key' => env('RAZORPAY_KEY')
        ], 200);

    } catch (\Razorpay\Api\Errors\Base $e) {
        // 🔴 Razorpay specific error
        Log::error('💥 Razorpay API error', [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
        ]);

        return response()->json([
            'error' => 'Razorpay error',
            'message' => $e->getMessage()
        ], 500);

    } catch (\Exception $e) {
        // 🔴 General error
        Log::error('💥 Subscription creation failed', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'error' => 'Failed to create subscription',
            'message' => $e->getMessage()
        ], 500);
    }
}

    // Step 2: Verify Payment Success
    public function verifyPayment(Request $request)
    {
        $input = $request->all();
        $paymentId = $input['razorpay_payment_id'];
        $subscriptionId = $input['razorpay_subscription_id'];
        $signature = $input['razorpay_signature'];

        try {
            $generatedSignature = hash_hmac('sha256', $paymentId . '|' . $subscriptionId, env('RAZORPAY_SECRET'));
            
            if (hash_equals($generatedSignature, $signature)) {
                // Save subscription to DB
                UserSubscription::create([
                    'user_id' => Auth::id(),
                    'razorpay_subscription_id' => $subscriptionId,
                    'razorpay_payment_id' => $paymentId,
                    'status' => 'active',
                    'razorpay_current_start' => now(),
                ]);

                return response()->json(['status' => 'success']);
            }
        } catch (\Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage());
        }

        return response()->json(['status' => 'failed'], 400);
    }
}
