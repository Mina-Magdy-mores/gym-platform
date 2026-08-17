<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('login:attacker@example.com127.0.0.1');
    }

    public function test_login_attempts_are_throttled_after_five_requests(): void
    {
        // First 5 login attempts are allowed to reach authentication logic
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => 'attacker@example.com',
                'password' => 'wrong-password',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 6th login attempt in the same minute is blocked with 429 Too Many Requests
        $response = $this->post('/login', [
            'email' => 'attacker@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }
}
