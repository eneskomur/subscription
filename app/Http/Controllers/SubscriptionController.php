<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->middleware(function ($request, $next) {
            if (Auth::user()->id != $request->user_id) {
                return response()->json([
                    'errors' => 'Unauthorized'
                ], 401);
            }
            return $next($request);
        });

        $this->subscriptionService = $subscriptionService;
    }

    public function createSubscription(Request $request)
    {
        try {
            $this->validate($request, [
                'renewed_at' => 'required|date',
                'expired_at' => 'required|date',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }

        try {
            $subscription = $this->subscriptionService->createSubscription($request->all());

            return response()->json([
                'message' => 'Subscription created successfully!',
                'subscription' => $subscription
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateSubscription(Request $request)
    {
        try {
            $this->validate($request, [
                'renewed_at' => 'date',
                'expired_at' => 'date',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }



        $request->merge(['subscription_id' => $request->subscription_id]);
        $subscription = $this->subscriptionService->getSubscription($request->all());
        if (!$subscription) {
            return response()->json([
                'errors' => 'Subscription not found'
            ], 404);
        }

        try {
            $subscription = $this->subscriptionService->updateSubscription($subscription, $request->all());

            return response()->json([
                'message' => 'Subscription updated successfully!',
                'subscription' => $subscription
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteSubscription(Request $request)
    {
        $request->merge(['subscription_id' => $request->subscription_id]);
        $subscription = $this->subscriptionService->getSubscription($request->all());
        if (!$subscription) {
            return response()->json([
                'errors' => 'Subscription not found'
            ], 404);
        }

        try {
            $subscription = $this->subscriptionService->deleteSubscription($subscription);

            return response()->json([
                'message' => 'Subscription deleted successfully!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function listSubscriptions()
    {
        try {
            $subscriptions = $this->subscriptionService->listSubscriptions();

            return response()->json([
                'subscriptions' => $subscriptions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
