<?php

namespace App\DTOs;

/**
 * RegisterDTO
 *
 * Objeto de transferência de dados para registro de usuário.
 * Princípios SOLID:
 * - SRP: Transporta dados do registro.
 * - OCP: Pode ser estendido para novos campos.
 * - LSP: Substituível por subclasses.
 * - ISP: Expõe apenas dados necessários.
 * - DIP: Controller depende do DTO, não do Request.
 */
class RegisterDTO
{
    public string $name;
    public string $email;
    public string $password;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
    }
}
