<?php

namespace App\Repositories;

use App\Models\Product;

/**
 * ProductRepository
 *
 * Abstrai o acesso ao banco para produtos.
 * Princípios SOLID:
 * - DIP: Controller/Service depende do repositório, não do Model.
 * - OCP: Pode ser estendido para novas fontes de dados ou regras.
 */
class ProductRepository
{
    /**
     * Retorna todos os produtos
     */
    public function all()
    {
        return Product::all();
    }

    /**
     * Busca produto por ID
     */
    public function find($id)
    {
        return Product::find($id);
    }

    /**
     * Cria novo produto
     */
    public function create(array $data)
    {
        return Product::create($data);
    }

    /**
     * Atualiza produto
     */
    public function update(Product $product, array $data)
    {
        $product->update($data);
        return $product;
    }

    /**
     * Remove produto
     */
    public function delete(Product $product)
    {
        $product->delete();
        return true;
    }
}
