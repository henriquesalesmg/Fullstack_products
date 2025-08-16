<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_can_list_products()
    {
        Product::factory()->count(3)->create();
        $response = $this->getJson('/api/products', $this->authenticate());
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'current_page',
                    'data',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ],
                'message',
                'errors',
            ]);
    }

    public function test_can_create_product()
    {
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock_quantity' => 10,
        ];
        $response = $this->postJson('/api/products', $data, $this->authenticate());
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'description', 'price', 'stock_quantity'],
                'message',
                'errors',
            ]);
    }

    public function test_can_show_product()
    {
        $product = Product::factory()->create();
        $response = $this->getJson('/api/products/' . $product->id, $this->authenticate());
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'description', 'price', 'stock_quantity'],
                'message',
                'errors',
            ]);
    }

    public function test_can_update_product()
    {
        $product = Product::factory()->create();
        $data = [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 199.99,
            'stock_quantity' => 20,
        ];
        $response = $this->putJson('/api/products/' . $product->id, $data, $this->authenticate());
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'description', 'price', 'stock_quantity'],
                'message',
                'errors',
            ]);
    }

    public function test_can_delete_product()
    {
        $product = Product::factory()->create();
        $response = $this->deleteJson('/api/products/' . $product->id, [], $this->authenticate());
        $response->assertStatus(200)
            ->assertJson([
                'data' => null,
                'message' => 'Product deleted successfully',
                'errors' => null,
            ]);
    }

    public function test_create_product_with_invalid_data()
    {
        $data = [
            'name' => '', // nome obrigatório
            'price' => -10, // preço inválido
            // falta description e stock_quantity
        ];
        $response = $this->postJson('/api/products', $data, $this->authenticate());
        $response->assertStatus(422)
            ->assertJsonStructure(['errors']);
    }
}
