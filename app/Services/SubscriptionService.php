<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use App\Services\TransactionService;
use PhpParser\Node\Expr\Cast\Object_;

class SubscriptionService
{
    protected $transactionService;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
    }

    public function createSubscription(array $userData): Subscription
    {
        $subscription = Subscription::create([
            'name' => 'Monthly Subscription',
            'user_id' => Auth::user()->id,
            'price' => '59.99',
            'renewed_at' => $userData['renewed_at'],
            'expired_at' => $userData['expired_at']
        ]);

        return $subscription;
    }

    public function updateSubscription(Subscription $subscription, array $userData): Subscription
    {
        $subscription->update($userData);

        return $subscription;
    }

    public function deleteSubscription(Subscription $subscription): void
    {
        $subscription->delete();
    }

    public function listSubscriptions(): Object
    {
        $subscriptions = Subscription::with('transactions')
            ->where('user_id', Auth::user()->id)
            ->get();

        return $subscriptions;
    }

    public function getSubscription(array $userData): Subscription
    {
        $subscription = Subscription::where('user_id', Auth::user()->id)
            ->where('id', $userData['subscription_id'])
            ->first();

        return $subscription;
    }

    public function listExpirePeriodSubscriptions(): Object
    {
        $subscriptions = Subscription::where('expired_at', '<', now()->addHour())->get();

        return $subscriptions;
    }

    public function renewSubscriptions(): void
    {
        $subscriptions = $this->listExpirePeriodSubscriptions();

        foreach ($subscriptions as $subscription) {
            $transaction = $this->transactionService->createTransaction([
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'price' => $subscription->price
            ]);

            $subscription = $this->updateSubscription($subscription, [
                'renewed_at' => now(),
                'expired_at' => $subscription->expired_at->addMonth()
            ]);
        }
    }
}
