<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nomesProdutosCasa = [
            'Caneca', 'Colher', 'Panela', 'Prato', 'Copo', 'Faca', 'Garfo', 'Pote', 'Jarra', 'Balde',
            'Escorredor', 'Ralador', 'Tigela', 'Assadeira', 'Frigideira', 'Espremedor', 'Abridor de Lata',
            'Forma', 'Travessa', 'Cafeteira', 'Liquidificador', 'Batedeira', 'Toalha de Mesa', 'Porta-Temperos',
            'Porta-Copos', 'Porta-Guardanapos', 'Saleiro', 'Açucareiro', 'Leiteira', 'Espátula', 'Concha',
            'Peneira', 'Tabua de Corte', 'Cesto', 'Escumadeira', 'Coador', 'Pipoqueira', 'Chaleira', 'Moedor',
            'Porta-Pão', 'Porta-Sabão', 'Porta-Detergente', 'Porta-Esponja', 'Porta-Talheres', 'Porta-Lixo'
        ];
        return [
            'name' => $this->faker->unique()->randomElement($nomesProdutosCasa),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
        ];
    }
}
