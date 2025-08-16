<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * SettingRequest
 *
 * Validação customizada para configurações de usuário.
 * Reflete SRP: separa validação do controller.
 */
class SettingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->user() ? $this->user()->id : null;
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).{6,}$/'
            ]
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'E-mail já cadastrado.',
            'password.regex' => 'A senha deve ter no mínimo 6 caracteres, incluindo 1 número, 1 caractere especial e 1 letra maiúscula.'
        ];
    }
}
