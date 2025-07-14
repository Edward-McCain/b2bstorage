<?php

namespace App\Http\Controllers;

use App\Models\ProductBalance;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\ReceiptPosition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductBalanceController extends Controller
{
    /**
     * Получить остатки товаров
     */
    public function index(Request $request): JsonResponse
    {
        $query = ProductBalance::with(['product', 'warehouse']);

        // Логируем входящие параметры для отладки
        Log::info('Balances filter params:', $request->all());

        // Фильтры
        if ($request->has('warehouse_id') && !empty($request->warehouse_id)) {
            $query->where('warehouse_id', $request->warehouse_id);
            Log::info('Applied warehouse filter:', ['warehouse_id' => $request->warehouse_id]);
        }

        if ($request->has('product_id') && !empty($request->product_id)) {
            $query->where('product_id', $request->product_id);
            Log::info('Applied product filter:', ['product_id' => $request->product_id]);
        }

        if ($request->has('min_quantity') && !empty($request->min_quantity)) {
            $query->where('quantity', '>=', $request->min_quantity);
            Log::info('Applied min quantity filter:', ['min_quantity' => $request->min_quantity]);
        }

        if ($request->has('max_quantity') && !empty($request->max_quantity)) {
            $query->where('quantity', '<=', $request->max_quantity);
            Log::info('Applied max quantity filter:', ['max_quantity' => $request->max_quantity]);
        }

        // Поиск по названию товара
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('article', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
            Log::info('Applied search filter:', ['search' => $search]);
        }

        $balances = $query->orderBy('quantity', 'desc')->paginate(50);

        // Логируем результат
        Log::info('Balances query result:', ['count' => $balances->count()]);

        // Получаем цены из последних оприходований для каждого товара
        $this->addPricesToBalances($balances->getCollection());

        return response()->json($balances);
    }

    /**
     * Получить остатки товаров (POST запрос с фильтрами)
     */
    public function filter(Request $request): JsonResponse
    {
        $query = ProductBalance::with(['product', 'warehouse']);

        // Логируем входящие параметры для отладки
        Log::info('Balances filter POST params:', $request->all());

        // Фильтры
        if ($request->has('warehouse_id') && !empty($request->warehouse_id)) {
            $query->where('warehouse_id', $request->warehouse_id);
            Log::info('Applied warehouse filter:', ['warehouse_id' => $request->warehouse_id]);
        }

        if ($request->has('product_id') && !empty($request->product_id)) {
            $query->where('product_id', $request->product_id);
            Log::info('Applied product filter:', ['product_id' => $request->product_id]);
        }

        if ($request->has('min_quantity') && !empty($request->min_quantity)) {
            $minQuantity = (int) $request->min_quantity;
            $query->where('quantity', '>=', $minQuantity);
            Log::info('Applied min quantity filter:', ['min_quantity' => $minQuantity]);
        }

        if ($request->has('max_quantity') && !empty($request->max_quantity)) {
            $maxQuantity = (int) $request->max_quantity;
            $query->where('quantity', '<=', $maxQuantity);
            Log::info('Applied max quantity filter:', ['max_quantity' => $maxQuantity]);
        }

        // Поиск по названию товара
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('article', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
            Log::info('Applied search filter:', ['search' => $search]);
        }

        $page = (int) $request->get('page', 1);
        $balances = $query->orderBy('quantity', 'desc')->paginate(50);

        // Логируем результат
        Log::info('Balances POST query result:', ['count' => $balances->count()]);

        // Получаем цены из последних оприходований для каждого товара
        $this->addPricesToBalances($balances->getCollection());

        return response()->json($balances);
    }

    /**
     * Получить остатки по складам
     */
    public function byWarehouse(Request $request): JsonResponse
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id'
        ]);

        $balances = ProductBalance::with(['product'])
            ->where('warehouse_id', $request->warehouse_id)
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'desc')
            ->get();

        // Получаем цены из последних оприходований для каждого товара
        $balances->transform(function ($balance) {
            // Получаем последнюю цену из оприходований для этого товара
            $lastReceiptPosition = \App\Models\ReceiptPosition::where('product_id', $balance->product_id)
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastReceiptPosition) {
                $balance->product->price = $lastReceiptPosition->price;
            } else {
                $balance->product->price = 0;
            }

            return $balance;
        });

        $totalValue = $balances->sum(function ($balance) {
            return $balance->quantity * ($balance->product->price ?? 0);
        });

        return response()->json([
            'balances' => $balances,
            'total_items' => $balances->count(),
            'total_quantity' => $balances->sum('quantity'),
            'total_value' => $totalValue
        ]);
    }

    /**
     * Получить остатки по товару
     */
    public function byProduct(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products_sklad,id'
        ]);

        $balances = ProductBalance::with(['warehouse'])
            ->where('product_id', $request->product_id)
            ->orderBy('quantity', 'desc')
            ->get();

        $totalQuantity = $balances->sum('quantity');

        return response()->json([
            'balances' => $balances,
            'total_quantity' => $totalQuantity,
            'warehouses_count' => $balances->count()
        ]);
    }

    /**
     * Получить сводку по остаткам
     */
    public function summary(Request $request): JsonResponse
    {
        $query = ProductBalance::with(['product', 'warehouse']);

        if ($request->has('warehouse_id') && !empty($request->warehouse_id)) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $balances = $query->get();

        // Получаем цены из последних оприходований для каждого товара
        $balances->transform(function ($balance) {
            // Получаем последнюю цену из оприходований для этого товара
            $lastReceiptPosition = \App\Models\ReceiptPosition::where('product_id', $balance->product_id)
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastReceiptPosition) {
                $balance->product->price = $lastReceiptPosition->price;
            } else {
                $balance->product->price = 0;
            }

            return $balance;
        });

        $summary = [
            'total_products' => $balances->unique('product_id')->count(),
            'total_warehouses' => $balances->unique('warehouse_id')->count(),
            'total_quantity' => $balances->sum('quantity'),
            'total_value' => $balances->sum(function ($balance) {
                return $balance->quantity * ($balance->product->price ?? 0);
            }),
            'low_stock_items' => $balances->where('quantity', '<=', 10)->count(),
            'out_of_stock_items' => $balances->where('quantity', 0)->count()
        ];

        // Топ товаров по количеству
        $topProducts = $balances->groupBy('product_id')
            ->map(function ($productBalances) {
                $product = $productBalances->first()->product;
                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'total_quantity' => $productBalances->sum('quantity'),
                    'warehouses_count' => $productBalances->count()
                ];
            })
            ->sortByDesc('total_quantity')
            ->take(10)
            ->values();

        // Топ складов по количеству товаров
        $topWarehouses = $balances->groupBy('warehouse_id')
            ->map(function ($warehouseBalances) {
                $warehouse = $warehouseBalances->first()->warehouse;
                return [
                    'warehouse_id' => $warehouse->id,
                    'warehouse_name' => $warehouse->name,
                    'total_quantity' => $warehouseBalances->sum('quantity'),
                    'products_count' => $warehouseBalances->count()
                ];
            })
            ->sortByDesc('total_quantity')
            ->take(10)
            ->values();

        return response()->json([
            'summary' => $summary,
            'top_products' => $topProducts,
            'top_warehouses' => $topWarehouses
        ]);
    }

    /**
     * Получить товары с низким остатком
     */
    public function lowStock(Request $request): JsonResponse
    {
        $threshold = $request->get('threshold', 10);

        $lowStockItems = ProductBalance::with(['product', 'warehouse'])
            ->where('quantity', '<=', $threshold)
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'asc')
            ->get();

        // Получаем цены из последних оприходований для каждого товара
        $lowStockItems->transform(function ($balance) {
            // Получаем последнюю цену из оприходований для этого товара
            $lastReceiptPosition = \App\Models\ReceiptPosition::where('product_id', $balance->product_id)
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastReceiptPosition) {
                $balance->product->price = $lastReceiptPosition->price;
            } else {
                $balance->product->price = 0;
            }

            return $balance;
        });

        return response()->json([
            'low_stock_items' => $lowStockItems,
            'threshold' => $threshold,
            'count' => $lowStockItems->count()
        ]);
    }

    /**
     * Получить товары с нулевым остатком
     */
    public function outOfStock(Request $request): JsonResponse
    {
        $outOfStockItems = ProductBalance::with(['product', 'warehouse'])
            ->where('quantity', 0)
            ->orderBy('product_id')
            ->get();

        // Получаем цены из последних оприходований для каждого товара
        $outOfStockItems->transform(function ($balance) {
            // Получаем последнюю цену из оприходований для этого товара
            $lastReceiptPosition = \App\Models\ReceiptPosition::where('product_id', $balance->product_id)
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastReceiptPosition) {
                $balance->product->price = $lastReceiptPosition->price;
            } else {
                $balance->product->price = 0;
            }

            return $balance;
        });

        return response()->json([
            'out_of_stock_items' => $outOfStockItems,
            'count' => $outOfStockItems->count()
        ]);
    }

    /**
     * Получить движение товаров
     */
    public function movements(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products_sklad,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date'
        ]);

        // Получаем информацию о товаре
        $product = \App\Models\ProductSklad::find($request->product_id);
        
        $query = DB::table('product_operations as po')
            ->leftJoin('products_sklad as p', 'po.product_id', '=', 'p.id')
            ->leftJoin('warehouses as w', 'po.warehouse_id', '=', 'w.id')
            ->select([
                'po.*',
                'p.name as product_name',
                'p.article as product_article',
                'w.name as warehouse_name'
            ])
            ->where('po.product_id', $request->product_id);

        if ($request->has('warehouse_id') && !empty($request->warehouse_id)) {
            $query->where('po.warehouse_id', $request->warehouse_id);
        }

        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->where('po.created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->where('po.created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $page = (int) $request->get('page', 1);
        $movements = $query->orderBy('po.created_at', 'desc')
            ->paginate(50, ['*'], 'page', $page);

        // Получаем последнюю цену товара
        $lastReceiptPosition = \App\Models\ReceiptPosition::where('product_id', $request->product_id)
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        $productPrice = $lastReceiptPosition ? $lastReceiptPosition->price : 0;

        return response()->json([
            'movements' => $movements,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'article' => $product->article
            ],
            'product_price' => $productPrice
        ]);
    }

    /**
     * Получить последнюю цену товара из оприходований
     */
    private function getProductPrice($productId)
    {
        $lastReceiptPosition = ReceiptPosition::where('product_id', $productId)
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastReceiptPosition ? $lastReceiptPosition->price : 0;
    }

    /**
     * Добавить цены к остаткам товаров
     */
    private function addPricesToBalances($balances)
    {
        return $balances->transform(function ($balance) {
            $balance->product->price = $this->getProductPrice($balance->product_id);
            return $balance;
        });
    }
} 