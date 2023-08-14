<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function testUserRegistration()
    {
        $response = $this->json('POST', '/api/register', [
            'name' => 'Enes Kömür',
            'email' => 'loremipsum@email.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'loremipsum@email.com']);
    }

    public function testInvalidUserRegistration()
    {
        $response = $this->json('POST', '/api/register', [
            'name' => 'Enes Kömür',
            'email' => 'invalidemail',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
    }
}
