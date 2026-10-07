<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_user_can_register(): void
    {
        $this
            ->postJson('/api/auth/register', [
                'name' => 'Ana',
                'email' => 'ana@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertCreated()
            ->assertJsonStructure(['user', 'token']);
    }

    public function test_invalid_login_is_rejected(): void
    {
        $this
            ->postJson('/api/auth/login', [
                'email' => 'missing@example.com',
                'password' => 'bad',
            ])
            ->assertStatus(422);
    }
}
