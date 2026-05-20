<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_new_user_registration_is_approved_and_redirected()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'account_type' => 'personal',
        ]);

        $response->assertRedirect(route('verify.show', ['email' => 'john@example.com']));

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'status' => User::STATUS_APPROVED,
        ]);
    }

    public function test_pending_user_cannot_log_in()
    {
        $user = User::create([
            'name' => 'Pending User',
            'email' => 'pending@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_BUYER,
            'status' => User::STATUS_PENDING,
        ]);

        $response = $this->post('/login', [
            'email' => 'pending@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(auth()->check());
    }

    public function test_approved_user_is_redirected_to_verify()
    {
        $user = User::create([
            'name' => 'Approved User',
            'email' => 'approved@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_BUYER,
            'status' => User::STATUS_APPROVED,
            'verification_code' => '123456',
        ]);

        $response = $this->post('/login', [
            'email' => 'approved@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('verify.show', ['email' => 'approved@example.com']));
        $this->assertFalse(auth()->check());
    }

    public function test_user_can_verify_with_correct_code()
    {
        $user = User::create([
            'name' => 'Approved User',
            'email' => 'approved@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_BUYER,
            'status' => User::STATUS_APPROVED,
            'verification_code' => '123456',
        ]);

        $response = $this->post('/verify-account', [
            'email' => 'approved@example.com',
            'verification_code' => '123456',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals(User::STATUS_ACTIVE, $user->status);
        $this->assertNull($user->verification_code);
    }

    public function test_user_fails_verification_with_incorrect_code()
    {
        $user = User::create([
            'name' => 'Approved User',
            'email' => 'approved@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_BUYER,
            'status' => User::STATUS_APPROVED,
            'verification_code' => '123456',
        ]);

        $response = $this->post('/verify-account', [
            'email' => 'approved@example.com',
            'verification_code' => '654321',
        ]);

        $response->assertSessionHasErrors('verification_code');
        
        $user->refresh();
        $this->assertEquals(User::STATUS_APPROVED, $user->status);
        $this->assertEquals('123456', $user->verification_code);
    }
}
