<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stats_authenticated()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/products/stats?year=' . date('Y'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'year',
                'counts'
            ]);
    }

    public function test_dashboard_stats_unauthenticated()
    {
        $response = $this->getJson('/api/products/stats?year=' . date('Y'));
        $response->assertStatus(401);
    }
}
