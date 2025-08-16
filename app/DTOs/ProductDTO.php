<?php

namespace App\DTOs;

/**
 * ProductDTO
 *
 * Objeto de transferência de dados para Produto.
 * Facilita o transporte seguro e validado de dados entre camadas.
 * Princípios SOLID:
 * - SRP: Responsável apenas por transportar dados do produto.
 * - OCP: Pode ser estendido para novos campos sem alterar o controller.
 * - LSP: Pode ser substituído por subclasses sem quebrar o controller.
 * - ISP: Expõe apenas os dados necessários.
 * - DIP: Controller depende do DTO, não do Request.
 */
class ProductDTO
{
    public string $name;
    public ?string $description;
    public float $price;
    public int $stock_quantity;

    /**
     * Construtor recebe array de dados (ex: $request->all())
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? null;
        $this->price = isset($data['price']) ? (float) $data['price'] : 0.0;
        $this->stock_quantity = isset($data['stock_quantity']) ? (int) $data['stock_quantity'] : 0;
    }
}
