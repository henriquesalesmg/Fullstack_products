<?php

namespace App\Http\Controllers;

/**
 * SettingController
 *
 * Princípios SOLID aplicados:
 * - SRP: Controla apenas configurações do sistema.
 * - OCP: Métodos podem ser estendidos sem modificar o controller base.
 * - LSP: Pode ser substituído por subclasses sem quebrar funcionalidades.
 * - ISP: Não força métodos desnecessários, apenas o que precisa.
 * - DIP: Depende de Requests e Models via injeção de dependência.
 */

use App\Http\Requests\SettingRequest;
use Illuminate\Support\Facades\{Auth, Hash};
use App\Models\User;
use Illuminate\Http\Request;
use App\DTOs\SettingDTO;

class SettingController extends Controller
{
    /**
     * Atualiza configurações do usuário usando SettingDTO para transportar dados.
     * Mantém funcionamento original, apenas organiza os dados via DTO.
     */
    public function update(SettingRequest $request)
    {
        $user = Auth::user();
        $dto = new SettingDTO($request->validated());
        $user->name = $dto->name;
        $user->email = $dto->email;
        $user->password = Hash::make($dto->password);
        $user->save();
        return response()->json(['message' => 'Configurações salvas com sucesso!']);
    }

    public function delete(Request $request)
    {
        $user = Auth::user();
        $user->delete();
        return response()->json(['message' => 'Conta excluída com sucesso!']);
    }
}
