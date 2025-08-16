<?php

namespace App\Services;

use App\Models\Product;
use App\DTOs\ProductDTO;
use App\Events\ProductCreated;

/**
 * ProductService
 *
 * Camada de serviço para operações de produto.
 * Princípios SOLID:
 * - SRP: Responsável apenas por regras de negócio de produto.
 * - OCP: Pode ser estendido para novas regras sem alterar controller.
 * - LSP: Substituível por subclasses.
 * - ISP: Expõe apenas métodos necessários.
 * - DIP: Controller depende do serviço, não do model.
 */
class ProductService
{
    /**
     * Cria um novo produto a partir do DTO
     */
    public function create(ProductDTO $dto): Product
    {
        $product = Product::create([
            'name' => $dto->name,
            'description' => $dto->description,
            'price' => $dto->price,
            'stock_quantity' => $dto->stock_quantity,
        ]);
        // Dispara evento
        event(new ProductCreated($product));
        return $product;
    }

    /**
     * Atualiza um produto existente
     */
    public function update(Product $product, ProductDTO $dto): Product
    {
        $product->update([
            'name' => $dto->name,
            'description' => $dto->description,
            'price' => $dto->price,
            'stock_quantity' => $dto->stock_quantity,
        ]);
        return $product;
    }

}
