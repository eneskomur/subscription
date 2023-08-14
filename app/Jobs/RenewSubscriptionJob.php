<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SubscriptionService;
use App\Services\TransactionService;

class RenewSubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $subscriptionService;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->subscriptionService = new SubscriptionService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->subscriptionService->renewSubscriptions();
    }
}
