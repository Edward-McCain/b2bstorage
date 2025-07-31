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
use Illuminate\Support\Facades\Auth;

class ProductBalanceController extends Controller
{
    /**
     * Получить остатки товаров
     */
    public function index(Request $request): JsonResponse
    {
        $query = ProductBalance::with(['product.images', 'product.categoryRelation', 'product.subcategoryRelation', 'warehouse']);

        // Фильтруем по складам текущего пользователя
        $query->whereHas('warehouse', function ($q) {
            $q->where('user_id', Auth::id());
        });

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
        
        // Преобразуем URL изображений и добавляем названия категорий
        $balances->getCollection()->transform(function($balance) {
            if ($balance->product->images) {
                $balance->product->images = $this->transformProductImages($balance->product->images);
            }
            
            // Добавляем названия категорий через аксессоры
            $balance->product->category_name = $balance->product->category_name;
            $balance->product->subcategory_name = $balance->product->subcategory_name;
            
            return $balance;
        });

        return response()->json($balances);
    }

    /**
     * Получить остатки товаров (POST запрос с фильтрами)
     */
    public function filter(Request $request): JsonResponse
    {
        $query = ProductBalance::with(['product.images', 'product.categoryRelation', 'product.subcategoryRelation', 'warehouse']);

        // Фильтруем по складам текущего пользователя
        $query->whereHas('warehouse', function ($q) {
            $q->where('user_id', Auth::id());
        });

        // Фильтруем по товарам текущего пользователя из products_sklad
        $userProductIds = \App\Models\ProductSklad::where('user_id', Auth::id())->pluck('id')->toArray();
        $query->whereIn('product_id', $userProductIds);

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

        // Расширенная фильтрация по полям товара
        if ($request->has('category') && !empty($request->category)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        if ($request->has('subcategory') && !empty($request->subcategory)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('subcategory', $request->subcategory);
            });
        }

        if ($request->has('country') && !empty($request->country)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('country', 'like', "%{$request->country}%");
            });
        }

        if ($request->has('supplier') && !empty($request->supplier)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('supplier', 'like', "%{$request->supplier}%");
            });
        }

        if ($request->has('article') && !empty($request->article)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('article', 'like', "%{$request->article}%");
            });
        }

        if ($request->has('code') && !empty($request->code)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('code', 'like', "%{$request->code}%");
            });
        }

        if ($request->has('external_code') && !empty($request->external_code)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('external_code', 'like', "%{$request->external_code}%");
            });
        }

        if ($request->has('unit') && !empty($request->unit)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('unit', 'like', "%{$request->unit}%");
            });
        }

        if ($request->has('weight') && !empty($request->weight)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('weight', 'like', "%{$request->weight}%");
            });
        }

        if ($request->has('volume') && !empty($request->volume)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('volume', 'like', "%{$request->volume}%");
            });
        }

        if ($request->has('vat') && !empty($request->vat)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('vat', 'like', "%{$request->vat}%");
            });
        }

        if ($request->has('min_stock') && !empty($request->min_stock)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('min_stock', 'like', "%{$request->min_stock}%");
            });
        }

        if ($request->has('stock_type') && !empty($request->stock_type)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('stock_type', 'like', "%{$request->stock_type}%");
            });
        }

        if ($request->has('packing') && !empty($request->packing)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('packing', 'like', "%{$request->packing}%");
            });
        }

        if ($request->has('accounting_type') && !empty($request->accounting_type)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('accounting_type', 'like', "%{$request->accounting_type}%");
            });
        }

        if ($request->has('traceable') && !empty($request->traceable)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('traceable', $request->traceable);
            });
        }

        if ($request->has('marking') && !empty($request->marking)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('marking', 'like', "%{$request->marking}%");
            });
        }

        if ($request->has('product_type') && !empty($request->product_type)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('product_type', 'like', "%{$request->product_type}%");
            });
        }

        if ($request->has('barcode_type') && !empty($request->barcode_type)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('barcode_type', 'like', "%{$request->barcode_type}%");
            });
        }

        if ($request->has('barcode') && !empty($request->barcode)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('barcode', 'like', "%{$request->barcode}%");
            });
        }

        if ($request->has('cash_register_tax') && !empty($request->cash_register_tax)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('cash_register_tax', 'like', "%{$request->cash_register_tax}%");
            });
        }

        if ($request->has('cash_register_type') && !empty($request->cash_register_type)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('cash_register_type', 'like', "%{$request->cash_register_type}%");
            });
        }

        // Фильтрация по дате создания
        if ($request->has('created_at') && !empty($request->created_at)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->whereDate('created_at', $request->created_at);
            });
        }

        $page = (int) $request->get('page', 1);
        $balances = $query->orderBy('quantity', 'desc')->paginate(50);

        // Логируем результат
        Log::info('Balances POST query result:', ['count' => $balances->count()]);

        // Получаем цены из последних оприходований для каждого товара
        $this->addPricesToBalances($balances->getCollection());
        
        // Преобразуем URL изображений и добавляем названия категорий
        $balances->getCollection()->transform(function($balance) {
            if ($balance->product->images) {
                $balance->product->images = $this->transformProductImages($balance->product->images);
            }
            
            // Добавляем названия категорий через аксессоры
            $balance->product->category_name = $balance->product->category_name;
            $balance->product->subcategory_name = $balance->product->subcategory_name;
            
            return $balance;
        });

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

        // Получаем ID товаров пользователя
        $userProductIds = \App\Models\ProductSklad::where('user_id', Auth::id())->pluck('id')->toArray();

        $balances = ProductBalance::with(['product.images', 'product.categoryRelation', 'product.subcategoryRelation'])
            ->whereIn('product_id', $userProductIds)
            ->where('warehouse_id', $request->warehouse_id)
            ->where('quantity', '>', 0)
            ->whereHas('warehouse', function ($q) {
                $q->where('user_id', Auth::id());
            })
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

            // Добавляем названия категорий через аксессоры
            $balance->product->category_name = $balance->product->category_name;
            $balance->product->subcategory_name = $balance->product->subcategory_name;

            return $balance;
        });

        // Преобразуем URL изображений
        $this->transformImageUrls($balances);

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

        // Проверяем, что товар принадлежит текущему пользователю
        $product = \App\Models\ProductSklad::where('id', $request->product_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Товар не найден'], 404);
        }

        $balances = ProductBalance::with(['warehouse'])
            ->where('product_id', $request->product_id)
            ->whereHas('warehouse', function ($q) {
                $q->where('user_id', Auth::id());
            })
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
        $user = Auth::user();
        $currency = $user && $user->currency ? $user->currency : 'UZS';

        // 1. Получаем количество складов пользователя
        $total_warehouses = \App\Models\Warehouse::where('user_id', $user->id)->count();

        // 2. Получаем количество товаров пользователя из products_sklad
        $userProductsQuery = \App\Models\ProductSklad::where('user_id', $user->id);
        if ($request->has('warehouse_id') && !empty($request->warehouse_id)) {
            $userProductsQuery->where('warehouse_id', $request->warehouse_id);
        }
        $userProducts = $userProductsQuery->get();
        $total_products = $userProducts->count();

        // 3. Получаем общее количество единиц товаров из product_balances
        $userProductIds = $userProducts->pluck('id')->toArray();
        $total_quantity = \App\Models\ProductBalance::whereIn('product_id', $userProductIds)
            ->whereHas('warehouse', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->sum('quantity');

        // 4. Считаем общую стоимость: количество * цена (из products_sklad или receipt_positions)
        $total_value = 0;
        $low_stock_items = 0;
        $out_of_stock_items = 0;

        foreach ($userProducts as $product) {
            // Получаем общее количество этого товара по всем складам
            $productTotalQuantity = \App\Models\ProductBalance::where('product_id', $product->id)
                ->whereHas('warehouse', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->sum('quantity');

            // Получаем цену товара (сначала из products_sklad, затем из receipt_positions)
            $productPrice = $this->getProductPrice($product->id);

            // Считаем стоимость для этого товара
            $productValue = $productTotalQuantity * $productPrice;
            $total_value += $productValue;

            // Считаем товары с низким остатком и без остатка
            if ($productTotalQuantity <= 10 && $productTotalQuantity > 0) {
                $low_stock_items++;
            }
            if ($productTotalQuantity <= 0) {
                $out_of_stock_items++;
            }
        }

        $summary = [
            'total_products' => $total_products,
            'total_warehouses' => $total_warehouses,
            'total_quantity' => $total_quantity,
            'total_value' => $total_value,
            'low_stock_items' => $low_stock_items,
            'out_of_stock_items' => $out_of_stock_items
        ];

        return response()->json([
            'summary' => $summary,
            'currency' => $currency,
            'top_products' => [],
            'top_warehouses' => []
        ]);
    }

    /**
     * Получить товары с низким остатком
     */
    public function lowStock(Request $request): JsonResponse
    {
        $threshold = $request->get('threshold', 10);

        // Получаем ID товаров пользователя
        $userProductIds = \App\Models\ProductSklad::where('user_id', Auth::id())->pluck('id')->toArray();

        $lowStockItems = ProductBalance::with(['product.images', 'warehouse'])
            ->whereIn('product_id', $userProductIds)
            ->where('quantity', '<=', $threshold)
            ->where('quantity', '>', 0)
            ->whereHas('warehouse', function ($q) {
                $q->where('user_id', Auth::id());
            })
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

        // Преобразуем URL изображений
        $this->transformImageUrls($lowStockItems);

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
        // Получаем ID товаров пользователя
        $userProductIds = \App\Models\ProductSklad::where('user_id', Auth::id())->pluck('id')->toArray();

        $outOfStockItems = ProductBalance::with(['product.images', 'warehouse'])
            ->whereIn('product_id', $userProductIds)
            ->where('quantity', 0)
            ->whereHas('warehouse', function ($q) {
                $q->where('user_id', Auth::id());
            })
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

        // Преобразуем URL изображений
        $this->transformImageUrls($outOfStockItems);

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

        // Проверяем, что товар принадлежит текущему пользователю
        $product = \App\Models\ProductSklad::where('id', $request->product_id)
            ->where('user_id', Auth::id())
            ->with(['categoryRelation', 'subcategoryRelation'])
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Товар не найден'], 404);
        }
        
        $query = DB::table('product_operations as po')
            ->leftJoin('products_sklad as p', 'po.product_id', '=', 'p.id')
            ->leftJoin('warehouses as w', 'po.warehouse_id', '=', 'w.id')
            ->select([
                'po.*',
                'p.name as product_name',
                'p.article as product_article',
                'w.name as warehouse_name'
            ])
            ->where('po.product_id', $request->product_id)
            ->where('w.user_id', Auth::id());

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
        $productPrice = 0;
        
        // Сначала проверяем цену в самом товаре
        if ($product && $product->price > 0) {
            $productPrice = (float) $product->price;
            Log::info('Цена товара взята из products_sklad', [
                'product_id' => $request->product_id,
                'price' => $productPrice
            ]);
        } else {
            // Если цены нет в товаре, проверяем последнее оприходование
            $lastReceiptPosition = \App\Models\ReceiptPosition::where('product_id', $request->product_id)
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->orderBy('created_at', 'desc')
                ->first();

            $productPrice = $lastReceiptPosition ? (float) $lastReceiptPosition->price : 0;
            
            Log::info('Цена товара взята из оприходований', [
                'product_id' => $request->product_id,
                'price' => $productPrice,
                'receipt_position_id' => $lastReceiptPosition ? $lastReceiptPosition->id : null
            ]);
        }
        
        Log::info('Итоговая цена товара для движений', [
            'product_id' => $request->product_id,
            'final_price' => $productPrice
        ]);

        return response()->json([
            'movements' => $movements,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'article' => $product->article,
                'category' => $product->category,
                'subcategory' => $product->subcategory,
                'category_name' => $product->category_name,
                'subcategory_name' => $product->subcategory_name
            ],
            'product_price' => $productPrice
        ]);
    }

    /**
     * Получить цену товара
     */
    private function getProductPrice($productId)
    {
        // Сначала пытаемся получить цену из самого товара
        $product = \App\Models\ProductSklad::find($productId);
        if ($product && $product->price > 0) {
            return (float) $product->price;
        }

        // Если цены нет в товаре, проверяем последнее оприходование (для совместимости)
        $lastReceiptPosition = ReceiptPosition::where('product_id', $productId)
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastReceiptPosition ? (float) $lastReceiptPosition->price : 0;
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

    /**
     * Преобразовать относительные пути изображений в полные URL
     */
    private function transformImageUrls($balances)
    {
        $baseUrl = request()->getSchemeAndHttpHost() . '/storage/';
        
        $balances->each(function ($balance) use ($baseUrl) {
            if ($balance->product && $balance->product->images) {
                $balance->product->images->each(function ($image) use ($baseUrl) {
                    $image->image_url = $baseUrl . $image->image_url;
                });
            }
        });
        
        return $balances;
    }

    /**
     * Преобразовать изображения товара (как в ProductController)
     */
    private function transformProductImages($images)
    {
        // Автоматически определяем базовый URL из текущего запроса
        $baseUrl = request()->getSchemeAndHttpHost() . '/storage/';
        
        return $images->map(function($image) use ($baseUrl) {
            $image->image_url = $baseUrl . $image->image_url;
            return $image;
        });
    }
} 