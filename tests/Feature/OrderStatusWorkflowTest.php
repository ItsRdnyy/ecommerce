<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderStatusWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $business;
    protected $buyer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->business = User::create([
            'name' => 'Business User',
            'email' => 'business@example.com',
            'password' => 'password123',
            'role' => User::ROLE_BUSINESS,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->buyer = User::create([
            'name' => 'Buyer User',
            'email' => 'buyer@example.com',
            'password' => 'password123',
            'role' => User::ROLE_BUYER,
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    public function test_business_cannot_access_other_business_orders()
    {
        $otherBusiness = User::create([
            'name' => 'Other Business',
            'email' => 'other@example.com',
            'password' => 'password123',
            'role' => User::ROLE_BUSINESS,
            'status' => User::STATUS_ACTIVE,
        ]);

        $order = Order::create([
            'buyer_id' => $this->buyer->id,
            'business_id' => $otherBusiness->id,
            'type' => 'retail',
            'status' => 'pending',
            'total' => 100.00,
        ]);

        $response = $this->actingAs($this->business)
            ->patch(route('business.orders.status', $order), [
                'status' => 'processing',
            ]);

        $response->assertStatus(403);
    }

    public function test_sequential_transitions_success_for_business()
    {
        $order = Order::create([
            'buyer_id' => $this->buyer->id,
            'business_id' => $this->business->id,
            'type' => 'retail',
            'status' => 'pending',
            'total' => 100.00,
        ]);

        // pending -> processing
        $response = $this->actingAs($this->business)
            ->patch(route('business.orders.status', $order), [
                'status' => 'processing',
            ]);
        $response->assertRedirect(route('business.orders'));
        $order->refresh();
        $this->assertEquals('processing', $order->status);

        // processing -> shipped
        $response = $this->actingAs($this->business)
            ->patch(route('business.orders.status', $order), [
                'status' => 'shipped',
            ]);
        $response->assertRedirect(route('business.orders'));
        $order->refresh();
        $this->assertEquals('shipped', $order->status);

        // shipped -> delivered
        $response = $this->actingAs($this->business)
            ->patch(route('business.orders.status', $order), [
                'status' => 'delivered',
            ]);
        $response->assertRedirect(route('business.orders'));
        $order->refresh();
        $this->assertEquals('delivered', $order->status);
    }

    public function test_invalid_transitions_fail_for_business()
    {
        $order = Order::create([
            'buyer_id' => $this->buyer->id,
            'business_id' => $this->business->id,
            'type' => 'retail',
            'status' => 'pending',
            'total' => 100.00,
        ]);

        // pending -> shipped should fail
        $response = $this->actingAs($this->business)
            ->patch(route('business.orders.status', $order), [
                'status' => 'shipped',
            ]);
        $response->assertSessionHas('error', 'Invalid status transition.');
        $order->refresh();
        $this->assertEquals('pending', $order->status);

        // Transition order to processing for next checks
        $order->update(['status' => 'processing']);

        // processing -> delivered should fail
        $response = $this->actingAs($this->business)
            ->patch(route('business.orders.status', $order), [
                'status' => 'delivered',
            ]);
        $response->assertSessionHas('error', 'Invalid status transition.');
        $order->refresh();
        $this->assertEquals('processing', $order->status);

        // Transition order to shipped for next checks
        $order->update(['status' => 'shipped']);

        // shipped -> cancelled should fail
        $response = $this->actingAs($this->business)
            ->patch(route('business.orders.status', $order), [
                'status' => 'cancelled',
            ]);
        $response->assertSessionHas('error', 'Invalid status transition.');
        $order->refresh();
        $this->assertEquals('shipped', $order->status);
    }

    public function test_cancellation_allowed_during_pending_and_processing_for_business()
    {
        $order1 = Order::create([
            'buyer_id' => $this->buyer->id,
            'business_id' => $this->business->id,
            'type' => 'retail',
            'status' => 'pending',
            'total' => 100.00,
        ]);

        $response = $this->actingAs($this->business)
            ->patch(route('business.orders.status', $order1), [
                'status' => 'cancelled',
            ]);
        $response->assertRedirect(route('business.orders'));
        $order1->refresh();
        $this->assertEquals('cancelled', $order1->status);

        $order2 = Order::create([
            'buyer_id' => $this->buyer->id,
            'business_id' => $this->business->id,
            'type' => 'retail',
            'status' => 'processing',
            'total' => 100.00,
        ]);

        $response = $this->actingAs($this->business)
            ->patch(route('business.orders.status', $order2), [
                'status' => 'cancelled',
            ]);
        $response->assertRedirect(route('business.orders'));
        $order2->refresh();
        $this->assertEquals('cancelled', $order2->status);
    }

    public function test_sequential_transitions_success_for_admin()
    {
        $order = Order::create([
            'buyer_id' => $this->buyer->id,
            'business_id' => $this->business->id,
            'type' => 'retail',
            'status' => 'pending',
            'total' => 100.00,
        ]);

        // pending -> processing
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.status', $order), [
                'status' => 'processing',
            ]);
        $response->assertRedirect();
        $order->refresh();
        $this->assertEquals('processing', $order->status);

        // processing -> shipped
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.status', $order), [
                'status' => 'shipped',
            ]);
        $response->assertRedirect();
        $order->refresh();
        $this->assertEquals('shipped', $order->status);

        // shipped -> delivered
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.status', $order), [
                'status' => 'delivered',
            ]);
        $response->assertRedirect();
        $order->refresh();
        $this->assertEquals('delivered', $order->status);
    }

    public function test_invalid_transitions_fail_for_admin()
    {
        $order = Order::create([
            'buyer_id' => $this->buyer->id,
            'business_id' => $this->business->id,
            'type' => 'retail',
            'status' => 'pending',
            'total' => 100.00,
        ]);

        // pending -> shipped should fail
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.status', $order), [
                'status' => 'shipped',
            ]);
        $response->assertSessionHas('error', 'Invalid status transition.');
        $order->refresh();
        $this->assertEquals('pending', $order->status);

        // Transition order to processing for next checks
        $order->update(['status' => 'processing']);

        // processing -> delivered should fail
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.status', $order), [
                'status' => 'delivered',
            ]);
        $response->assertSessionHas('error', 'Invalid status transition.');
        $order->refresh();
        $this->assertEquals('processing', $order->status);

        // Transition order to shipped for next checks
        $order->update(['status' => 'shipped']);

        // shipped -> cancelled should fail
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.status', $order), [
                'status' => 'cancelled',
            ]);
        $response->assertSessionHas('error', 'Invalid status transition.');
        $order->refresh();
        $this->assertEquals('shipped', $order->status);
    }
}
