<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $anoAtual = Carbon::now()->year;
        $mesAtual = Carbon::now()->month;
        $nomesProdutosCasa = [
            'Caneca', 'Colher', 'Panela', 'Prato', 'Copo', 'Faca', 'Garfo', 'Pote', 'Jarra', 'Balde',
            'Escorredor', 'Ralador', 'Tigela', 'Assadeira', 'Frigideira', 'Espremedor', 'Abridor de Lata',
            'Forma', 'Travessa', 'Cafeteira', 'Liquidificador', 'Batedeira', 'Toalha de Mesa', 'Porta-Temperos',
            'Porta-Copos', 'Porta-Guardanapos', 'Saleiro', 'Açucareiro', 'Leiteira', 'Espátula', 'Concha',
            'Peneira', 'Tabua de Corte', 'Cesto', 'Escumadeira', 'Coador', 'Pipoqueira', 'Chaleira', 'Moedor',
            'Porta-Pão', 'Porta-Sabão', 'Porta-Detergente', 'Porta-Esponja', 'Porta-Talheres', 'Porta-Lixo',
            'Cadeira', 'Mesa', 'Armário', 'Sofá', 'Estante', 'Banco', 'Poltrona', 'Vaso', 'Tapete', 'Luminária'
        ];
        $produtosCriados = 0;
        for ($mes = 1; $mes <= $mesAtual; $mes++) {
            $quantidadeProdutos = rand(5, 15);
            $nomesUsados = [];
            for ($i = 1; $i <= $quantidadeProdutos; $i++) {
                // Garante nomes únicos por mês
                do {
                    $nomeProduto = $nomesProdutosCasa[array_rand($nomesProdutosCasa)];
                } while (in_array($nomeProduto, $nomesUsados));
                $nomesUsados[] = $nomeProduto;
                $date = Carbon::create($anoAtual, $mes, rand(1, 28), rand(0, 23), rand(0, 59), rand(0, 59));
                \App\Models\Product::firstOrCreate(
                    ['name' => $nomeProduto],
                    [
                        'description' => 'Descrição do produto ' . $nomeProduto,
                        'price' => rand(100, 1000) / 1.57,
                        'stock_quantity' => rand(1, 100),
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]
                );
                $produtosCriados++;
            }
        }
    }
}
