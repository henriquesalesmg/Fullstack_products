<?php

namespace App\Http\Controllers;

/**
 * ProductController
 *
 * Princípios SOLID aplicados:
 * - SRP: Controla apenas operações de produto.
 * - OCP: Métodos podem ser estendidos sem modificar o controller base.
 * - LSP: Pode ser substituído por subclasses sem quebrar funcionalidades.
 * - ISP: Não força métodos desnecessários, apenas o que precisa.
 * - DIP: Depende de Requests e Models via injeção de dependência.
 */

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\Log;
use App\DTOs\ProductDTO;
use App\Services\ProductService;

class ProductController extends Controller
{
    /**
     * Service de produto, responsável pelas regras de negócio.
     */
    protected ProductService $service;

    public function __construct()
    {
        $this->service = new ProductService();
    }
    /**
     * Estatísticas de produtos adicionados por mês no ano corrente.
     */
    public function stats(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $counts = array_fill(0, 12, 0);
        $products = Product::selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->get();
        foreach ($products as $row) {
            $counts[$row->month - 1] = (int) $row->total;
        }
        return response()->json([
            'year' => (int) $year,
            'counts' => $counts
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();
        // Filtro de pesquisa (case-insensitive, nome parcial)
        if ($search = $request->query('search')) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($search) . '%']);
        }
        // Ordenação
        $orderBy = $request->query('order_by', 'id');
        $orderDir = $request->query('order_dir', 'desc');
        $allowedOrder = ['id', 'name', 'price', 'stock_quantity'];
        if (!in_array($orderBy, $allowedOrder)) $orderBy = 'id';
        if (!in_array(strtolower($orderDir), ['asc', 'desc'])) $orderDir = 'desc';
        // Filtro de preço mínimo
        if ($priceMin = $request->query('price_min')) {
            $query->where('price', '>=', $priceMin);
        }
        // Filtro de preço máximo
        if ($priceMax = $request->query('price_max')) {
            $query->where('price', '<=', $priceMax);
        }
        // Filtro de estoque mínimo
        if ($stockMin = $request->query('stock_min')) {
            $query->where('stock_quantity', '>=', $stockMin);
        }
        // Filtro de estoque máximo
        if ($stockMax = $request->query('stock_max')) {
            $query->where('stock_quantity', '<=', $stockMax);
        }

        if ($request->query('all') == 1) {
            $products = $query->orderBy($orderBy, $orderDir)->get();
            return response()->json([
                'items' => $products,
                'total' => $products->count(),
                'data' => [
                    'current_page' => 1,
                    'data' => $products,
                    'first_page_url' => null,
                    'from' => 1,
                    'last_page' => 1,
                    'last_page_url' => null,
                    'links' => [],
                    'next_page_url' => null,
                    'path' => url('/api/products'),
                    'per_page' => $products->count(),
                    'prev_page_url' => null,
                    'to' => $products->count(),
                    'total' => $products->count(),
                ],
                'message' => 'Product list',
                'errors' => null
            ]);
        } else {
            $perPage = $request->query('per_page', 50);
            $products = $query->orderBy($orderBy, $orderDir)->paginate($perPage);
            return response()->json([
                'items' => $products->items(),
                'total' => $products->total(),
                'data' => [
                    'current_page' => $products->currentPage(),
                    'data' => $products->items(),
                    'first_page_url' => $products->url(1),
                    'from' => $products->firstItem(),
                    'last_page' => $products->lastPage(),
                    'last_page_url' => $products->url($products->lastPage()),
                    'links' => $products->links()->toHtml(),
                    'next_page_url' => $products->nextPageUrl(),
                    'path' => $products->path(),
                    'per_page' => $products->perPage(),
                    'prev_page_url' => $products->previousPageUrl(),
                    'to' => $products->lastItem(),
                    'total' => $products->total(),
                ],
                'message' => 'Product list',
                'errors' => null
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Agora usando ProductDTO para transportar e validar dados.
     * Mantém funcionamento original, apenas organiza os dados via DTO.
     */
    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();
        // Garante que o preço está no formato float, mesmo se vier como string BRL
        if (isset($data['price'])) {
            if (is_string($data['price'])) {
                // Aceita tanto "1000,99" quanto "1000.99" ou "R$ 1.000,99"
                $clean = str_replace(['R$', ' ', '.'], ['', '', ''], $data['price']);
                $clean = str_replace(',', '.', $clean);
                $data['price'] = floatval($clean);
            }
        }
        $dto = new ProductDTO($data);
        $product = $this->service->create($dto);
        return response()->json([
            'data' => $product,
            'message' => 'Product created successfully',
            'errors' => null
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'data' => null,
                'message' => 'Product not found',
                'errors' => ['Product not found']
            ], 404);
        }
        return response()->json([
            'data' => $product,
            'message' => 'Product details',
            'errors' => null
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'data' => null,
                'message' => 'Product not found',
                'errors' => ['Product not found']
            ], 404);
        }
        $data = $request->validated();
        if (isset($data['price'])) {
            if (is_string($data['price'])) {
                $clean = str_replace(['R$', ' ', '.'], ['', '', ''], $data['price']);
                $clean = str_replace(',', '.', $clean);
                $data['price'] = floatval($clean);
            }
        }
        $dto = new ProductDTO($data);
        $product = $this->service->update($product, $dto);
        return response()->json([
            'data' => $product,
            'message' => 'Product updated successfully',
            'errors' => null
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'data' => null,
                'message' => 'Product not found',
                'errors' => ['Product not found']
            ], 404);
        }
        $product->delete();
        return response()->json([
            'data' => null,
            'message' => 'Product deleted successfully',
            'errors' => null
        ]);
    }

        /**
         * Retorna estatísticas de ganhos dos produtos.
         * Exemplo: total de produtos e soma dos preços.
         */
        public function earnings(Request $request)
        {
            $now = now();
            $currentMonth = Product::whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->sum('price');

            $previousMonthDate = $now->copy()->subMonth();
            $previousMonth = Product::whereYear('created_at', $previousMonthDate->year)
                ->whereMonth('created_at', $previousMonthDate->month)
                ->sum('price');

            $totalProducts = Product::count();
            $totalEarnings = Product::sum('price');
            return response()->json([
                'total_products' => $totalProducts,
                'total_earnings' => $totalEarnings,
                'currentMonth' => $currentMonth,
                'previousMonth' => $previousMonth,
                'message' => 'Resumo dos ganhos dos produtos',
                'errors' => null
            ]);
        }
}
