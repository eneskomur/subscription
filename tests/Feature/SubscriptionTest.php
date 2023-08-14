<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Subscription;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateSubscription()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->json('POST', "/api/user/{$user->id}/subscription", [
                'renewed_at' => now(),
                'expired_at' => now()->addMonth(),
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('subscriptions', ['user_id' => $user->id]);
    }

    public function testUpdateSubscription()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->json('POST', "/api/user/{$user->id}/subscription", [
                'renewed_at' => now(),
                'expired_at' => now()->addMonth(),
            ]);

        $response->assertStatus(201);

        $subscription = Subscription::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)
            ->json('PUT', "/api/user/{$user->id}/subscription/{$subscription->id}", [
                'renewed_at' => now(),
                'expired_at' => now()->addMonths(2),
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('subscriptions', ['id' => $subscription->id, 'expired_at' => now()->addMonths(2)]);
    }

    public function testDeleteSubscription()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->json('POST', "/api/user/{$user->id}/subscription", [
                'renewed_at' => now(),
                'expired_at' => now()->addMonth(),
            ]);

        $response->assertStatus(201);

        $subscription = Subscription::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)
            ->json('DELETE', "/api/user/{$user->id}/subscription/{$subscription->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('subscriptions', ['id' => $subscription->id, 'deleted_at' => null]);
    }

    public function testCreateSubscriptionTransaction()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->json('POST', "/api/user/{$user->id}/subscription", [
                'renewed_at' => now(),
                'expired_at' => now()->addMonth(),
            ]);

        $response->assertStatus(201);

        $subscription = Subscription::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)
            ->json('POST', "/api/user/{$user->id}/transaction", [
                'subscription_id' => $subscription->id,
                'price' => '59.99',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('transactions', ['subscription_id' => $subscription->id]);
    }
}
