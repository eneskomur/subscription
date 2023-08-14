<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TransactionService;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $transactionService;
    protected $subscriptionService;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::user()->id != $request->user_id) {
                return response()->json([
                    'errors' => 'Unauthorized'
                ], 401);
            }
            return $next($request);
        });

        $this->transactionService = new TransactionService();
        $this->subscriptionService = new SubscriptionService();
    }

    public function createTransaction(Request $request)
    {
        try {
            $this->validate($request, [
                'subscription_id' => 'required|integer',
                'price' => 'required|numeric',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }

        $subscription = $this->subscriptionService->getSubscription($request->all());
        if (!$subscription) {
            return response()->json([
                'errors' => 'Subscription not found'
            ], 404);
        }

        try {
            $request->merge(['user_id' => $subscription->user_id]);
            $transaction = $this->transactionService->createTransaction($request->all());

            $subscription = $this->subscriptionService->updateSubscription($subscription, [
                'renewed_at' => now(),
                'expired_at' => $subscription->expired_at->addMonth()
            ]);

            return response()->json([
                'message' => 'Transaction created successfully!',
                'transaction' => $transaction
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
