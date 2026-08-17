<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthAndRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles in memory
        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('trainer', 'web');
        Role::findOrCreate('member', 'web');
    }

    public function test_newly_registered_user_is_automatically_assigned_the_member_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Kareem Tarek',
            'email' => 'kareem@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'gender' => 'male',
            'phone' => '01012345678',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'kareem@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('member'));
        $this->assertFalse($user->is_blocked);
    }

    public function test_unauthenticated_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_blocked_user_is_denied_access_and_logged_out(): void
    {
        $user = User::factory()->create([
            'is_blocked' => true,
        ]);
        $user->assignRole('member');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
