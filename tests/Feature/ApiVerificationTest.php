<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_authentication_and_routing()
    {
        // 1. Setup Data
        $tenant = Tenant::create([
            'name' => 'Test Business',
            'business_type' => 'Supermarket',
            'email' => 'john@test.com',
            'phone' => '1234567890',
            'subscription_plan' => 'Pro',
        ]);

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'tenant_id' => $tenant->id,
        ]);

        $category = Category::create([
            'tenant_id' => $tenant->id,
            'name' => 'Beverages',
        ]);

        Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'name' => 'Cola',
            'price' => 2.50,
            'cost_price' => 1.00,
            'stock_quantity' => 100,
        ]);

        // 2. Test Unauthenticated Access is Blocked
        $response = $this->getJson('/api/products');
        $response->assertStatus(401);

        // 3. Test Authentication (Login)
        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
            'device_name' => 'test-device',
        ]);
        
        $loginResponse->assertStatus(200);
        $loginResponse->assertJsonStructure(['token', 'user']);
        
        $token = $loginResponse->json('token');

        // 4. Test Authenticated Access and Resource Formatting
        $productResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/products');

        $productResponse->assertStatus(200);
        
        // Assert proper Resource formatting (nested category, typed data)
        $productResponse->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'price',
                    'stock_quantity',
                    'category' => [
                        'id',
                        'name'
                    ]
                ]
            ]
        ]);
        
        $this->assertEquals('Cola', $productResponse->json('data.0.name'));
        $this->assertEquals('Beverages', $productResponse->json('data.0.category.name'));
    }
}
