<?php

namespace App\Http\Controllers;

/**
 * AuthController
 * 
 * Princípios SOLID aplicados:
 * - SRP: Controla autenticação e recuperação de senha.
 * - OCP: Métodos podem ser estendidos sem modificar o controller base.
 * - LSP: Pode ser substituído por subclasses sem quebrar funcionalidades.
 * - ISP: Não força métodos desnecessários, apenas o que precisa.
 * - DIP: Depende de Requests e Models via injeção de dependência.
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Password, DB};
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    /**
     * Retorna os dados do usuário autenticado.
     */
    public function user(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'data' => null,
                'message' => 'Usuário não autenticado',
                'errors' => ['Usuário não autenticado']
            ], 401);
        }
        return response()->json([
            'data' => $user,
            'message' => 'Usuário autenticado',
            'errors' => null
        ]);
    }
    // Verifica usuário por email e data de nascimento
    public function verifyUserForReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'birthdate' => 'required|date',
        ]);
        $user = User::where('email', $request->email)
            ->whereDate('birth_date', $request->birthdate)
            ->first();
        if ($user) {
            return response()->json(['userId' => $user->id]);
        } else {
            return response()->json(['message' => 'Dados não conferem.'], 404);
        }
    }

    // Reset de senha customizado via userId
    public function customResetPassword(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $user = User::find($request->userId);
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json(['message' => 'Senha alterada com sucesso.']);
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'birth_date' => 'required|date',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'birth_date' => Carbon::parse($request->birth_date)->format('Y-m-d'),
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'token' => $token,
            ],
            'message' => 'Usuário registrado com sucesso',
            'errors' => null
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais estão incorretas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => ['token' => $token, 'user' => $user],
            'message' => 'Login realizado com sucesso',
            'errors' => null
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'data' => null,
            'message' => 'Logout realizado com sucesso',
            'errors' => null
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink(
            $request->only('email')
        );
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'data' => null,
                'message' => __($status),
                'errors' => null
            ]);
        }
        return response()->json([
            'data' => null,
            'message' => __($status),
            'errors' => null
        ], 400);
    }

    // Alternativa: gerar token de reset e exibir na resposta
    public function requestPasswordReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json([
                'data' => null,
                'message' => 'User not found.',
                'errors' => ['email' => ['User not found.']]
            ], 404);
        }
        $token = bin2hex(random_bytes(32));
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => now()]
        );
        return response()->json([
            'data' => ['reset_token' => $token],
            'message' => 'Password reset token generated.',
            'errors' => null
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed'
        ]);
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();
        if (! $record) {
            return response()->json([
                'data' => null,
                'message' => 'Invalid token.',
                'errors' => ['token' => ['Invalid token.']]
            ], 400);
        }
        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json([
                'data' => null,
                'message' => 'User not found.',
                'errors' => ['email' => ['User not found.']]
            ], 404);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        return response()->json([
            'data' => null,
            'message' => 'Password reset successfully.',
            'errors' => null
        ]);
    }
}
