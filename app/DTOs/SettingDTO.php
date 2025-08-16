<?php

namespace App\DTOs;

/**
 * SettingDTO
 *
 * Objeto de transferência de dados para configurações de usuário.
 * Princípios SOLID:
 * - SRP: Transporta dados de configuração.
 * - OCP: Pode ser estendido para novos campos.
 * - LSP: Substituível por subclasses.
 * - ISP: Expõe apenas dados necessários.
 * - DIP: Controller depende do DTO, não do Request.
 */
class SettingDTO
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
