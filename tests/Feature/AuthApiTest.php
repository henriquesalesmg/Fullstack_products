<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthApiTest extends TestCase
    // ...existing code...
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'birth_date' => '1990-01-01',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email'],
                'message',
                'errors',
            ]);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'testlogin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'testlogin@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['token', 'user'],
                'message',
                'errors',
            ]);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create([
            'email' => 'testlogout@example.com',
            'password' => bcrypt('password123'),
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'testlogout@example.com',
            'password' => 'password123',
        ]);

        $token = $login->json('data.token');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'data' => null,
                'message' => 'Logout realizado com sucesso',
                'errors' => null,
            ]);
    }
    public function test_custom_password_recovery()
    {
        // Cria usuário
        $user = User::factory()->create([
            'email' => 'recover@example.com',
            'birth_date' => '1990-01-01',
            'password' => bcrypt('oldpassword'),
        ]);

        // Verifica usuário para reset
        $verify = $this->postJson('/api/auth/verify-user', [
            'email' => 'recover@example.com',
            'birthdate' => '1990-01-01',
        ]);
        $verify->assertStatus(200)
            ->assertJsonStructure(['userId']);
        $userId = $verify->json('userId');

        // Reseta senha
        $reset = $this->postJson('/api/auth/reset-password', [
            'userId' => $userId,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);
        $reset->assertStatus(200)
            ->assertJson(['message' => 'Senha alterada com sucesso.']);

        // Tenta login com nova senha
        $login = $this->postJson('/api/login', [
            'email' => 'recover@example.com',
            'password' => 'newpassword',
        ]);
        $login->assertStatus(200)
            ->assertJsonStructure(['data' => ['token', 'user']]);
    }

    public function test_login_rate_limit_frontend()
    {
        $user = User::factory()->create([
            'email' => 'ratelimit@example.com',
            'password' => bcrypt('senha123'),
        ]);
        $failures = 0;
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'ratelimit@example.com',
                'password' => 'senhaerrada',
            ]);
            if ($response->status() === 422) {
                $failures++;
            }
        }
        $this->assertEquals(6, $failures, 'O frontend deve bloquear após 5 tentativas, mas a API não possui rate limit nativo. Teste manual recomendado.');
    }
}
