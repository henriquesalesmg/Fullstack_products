<?php

namespace App\Http\Controllers;

/**
 * RegisterController
 * 
 * Princípios SOLID aplicados:
 * - SRP: Controla apenas registro de usuários.
 * - OCP: Métodos podem ser estendidos sem modificar o controller base.
 * - LSP: Pode ser substituído por subclasses sem quebrar funcionalidades.
 * - ISP: Não força métodos desnecessários, apenas o que precisa.
 * - DIP: Depende de Requests e Models via injeção de dependência.
 */

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\DTOs\RegisterDTO;
use App\Events\UserRegistered;

class RegisterController extends Controller
{
    /**
     * Registra novo usuário usando RegisterDTO e validação customizada via Form Request.
     * Mantém funcionamento original, apenas separa validação do controller.
     */
    public function register(RegisterRequest $request)
    {
        $dto = new RegisterDTO($request->validated());
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);
        // Dispara evento
        event(new UserRegistered($user));

        // Autentica e retorna token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => $user
            ]
        ]);
    }
}
