<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductSklad;
use App\Models\ProductImage;
use App\Models\ReceiptPosition;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use App\Models\ProductBalance;
use App\Models\ProductOperation;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Создать черновик товара
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeDraft(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $user = Auth::user();
        
        // Проверяем, есть ли пользователь
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }
        
        $data = [
            'user_id' => $user->id,
            'name' => $request->name,
        ];
        if ($request->has('fields')) {
            $data['fields'] = $request->fields;
        }
        $product = ProductSklad::create($data);
        
        return response()->json(['id' => $product->id], 201);
    }

    /**
     * Загрузить изображение для товара
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'alt_text' => 'nullable|string|max:255',
        ]);
        
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }
        
        $product = ProductSklad::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$product) {
            return response()->json(['error' => 'Товар не найден или доступ запрещен'], 404);
        }
        $file = $request->file('image');
        $filename = uniqid('product_', true) . '.webp';
        $path = 'uploads/products/' . $filename;
        // Конвертация в webp
        $imageData = file_get_contents($file->getPathname());
        $image = imagecreatefromstring($imageData);
        if ($image !== false) {
            // Сохраняем на диск public
            $fullPath = storage_path('app/public/' . $path);
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0777, true);
            }
            imagewebp($image, $fullPath, 90);
            imagedestroy($image);
        } else {
            // Если не удалось создать изображение, сохраняем оригинал
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
        }
        // Сохраняем в БД
        $img = ProductImage::create([
            'product_id' => $product->id,
            'image_url' => $path,
            'alt_text' => $request->alt_text ?? '',
        ]);
        
        // Преобразуем URL изображения в полный URL
        $images = collect([$img]);
        $transformedImages = $this->transformImageUrls($images);
        
        return response()->json(['image' => $transformedImages->first()], 201);
    }

    /**
     * Получить изображения для товара
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getImages($id)
    {
        $images = ProductImage::where('product_id', $id)->orderBy('created_at', 'asc')->get();
        
        // Преобразуем URL изображений
        $images = $this->transformImageUrls($images);
        
        return response()->json(['images' => $images]);
    }

    /**
     * Обновить товар
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        Log::info('Product update request', ['id' => $id, 'data' => $request->all()]);
        
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }
        
        $product = ProductSklad::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$product) {
            return response()->json(['error' => 'Товар не найден'], 404);
        }
        
        // Валидация данных
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|string',
            'subcategory_id' => 'nullable|string',
            'country' => 'nullable|string',
            'supplier' => 'nullable|string|max:255',
            'article' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'external_code' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'start_count' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'vat' => 'nullable|string|max:255',
            'packing' => 'nullable|string|max:255',
            'accounting_type' => 'nullable|string|max:255',
            'marking' => 'nullable|string|max:255',
            'product_type' => 'nullable|string|max:255',
            'barcode_type' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'cash_register_tax' => 'nullable|string|max:255',
            'cash_register_type' => 'nullable|string|max:255',
            // 'warehouse_id' => 'required|integer', // warehouse_id больше не обязателен
        ]);

        // Обновляем товар
        $updateData = [
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category_id,
            'subcategory' => $request->subcategory_id,
            'country' => $request->country,
            'supplier' => $request->supplier,
            'article' => $request->article,
            'code' => $request->code,
            'external_code' => $request->external_code,
            'unit' => $request->unit,
            'weight' => $request->weight,
            'volume' => $request->volume,
            'vat' => $request->vat,
            'packing' => $request->packing,
            'accounting_type' => $request->accounting_type,
            'marking' => $request->marking,
            'product_type' => $request->product_type,
            'barcode_type' => $request->barcode_type,
            'barcode' => $request->barcode,
            'cash_register_tax' => $request->cash_register_tax,
            'cash_register_type' => $request->cash_register_type,
            'start_count' => $request->start_count,
            'price' => $request->price,
        ];
        if ($request->has('warehouse_id') && $request->warehouse_id) {
            $updateData['warehouse_id'] = $request->warehouse_id;
        }
        if ($request->has('fields')) {
            $updateData['fields'] = $request->fields;
        }
        $product->update($updateData);

        // Обновляем остатки товара на основе начального остатка
        try {
            $warehouseId = $request->has('warehouse_id') && $request->warehouse_id ? $request->warehouse_id : $product->warehouse_id;
            ProductBalance::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouseId
                ],
                [
                    'quantity' => $request->start_count
                ]
            );

            Log::info('Остатки товара обновлены', [
                'product_id' => $product->id,
                'warehouse_id' => $warehouseId,
                'start_count' => $request->start_count
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка обновления остатков товара', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Товар успешно обновлен',
            'product' => $product
        ]);
    }

    /**
     * Удалить изображение товара
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteImage($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }
        
        $image = ProductImage::with('product')
            ->whereHas('product', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->find($id);
            
        if (!$image) {
            return response()->json(['error' => 'Изображение не найдено или доступ запрещен'], 404);
        }
        
        // Удалить файл
        if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
            Storage::disk('public')->delete($image->image_url);
        }
        $image->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Преобразовать относительные пути изображений в полные URL
     * @param \Illuminate\Database\Eloquent\Collection $images
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function transformImageUrls($images)
    {
        // Автоматически определяем базовый URL из текущего запроса
        $baseUrl = request()->getSchemeAndHttpHost() . '/storage/';
        
        return $images->map(function($image) use ($baseUrl) {
            $image->image_url = $baseUrl . $image->image_url;
            return $image;
        });
    }

    /**
     * Получить список товаров с пагинацией
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $perPage = $request->get('per_page', 15);
        $search = $request->get('search', '');
        
        $query = ProductSklad::where('user_id', $user->id)
            ->with([
                'images' => function($query) {
                    $query->orderBy('created_at', 'asc')->limit(1);
                },
                'categoryRelation',
                'subcategoryRelation',
                'warehouse'
            ])
            ->leftJoin('product_balances', 'products_sklad.id', '=', 'product_balances.product_id')
            ->selectRaw('products_sklad.*, COALESCE(SUM(product_balances.quantity), 0) as total_quantity')
            ->groupBy('products_sklad.id');

        // Поиск по названию, коду, артикулу
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%")
                  ->orWhere('article', 'ilike', "%{$search}%");
            });
        }

        // Фильтрация по категории
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Фильтрация по подкатегории
        if ($request->has('subcategory') && $request->subcategory) {
            $query->where('subcategory', $request->subcategory);
        }

        // Фильтрация по стране
        if ($request->has('country') && $request->country) {
            $query->where('country', $request->country);
        }

        // Фильтрация по количеству
        if ($request->has('quantity') && $request->quantity) {
            $query->havingRaw('COALESCE(SUM(product_balances.quantity), 0) = ?', [$request->quantity]);
        }

        // Фильтрация по артикулу
        if ($request->has('article') && $request->article) {
            $query->where('article', 'ilike', "%{$request->article}%");
        }

        // Фильтрация по коду
        if ($request->has('code') && $request->code) {
            $query->where('code', 'ilike', "%{$request->code}%");
        }

        // Фильтрация по внешнему коду
        if ($request->has('external_code') && $request->external_code) {
            $query->where('external_code', 'ilike', "%{$request->external_code}%");
        }

        // Фильтрация по единице измерения
        if ($request->has('unit') && $request->unit) {
            $query->where('unit', 'ilike', "%{$request->unit}%");
        }

        // Фильтрация по упаковке
        if ($request->has('packing') && $request->packing) {
            $query->where('packing', 'ilike', "%{$request->packing}%");
        }

        // Фильтрация по типу учета
        if ($request->has('accounting_type') && $request->accounting_type) {
            $query->where('accounting_type', 'ilike', "%{$request->accounting_type}%");
        }

        // Фильтрация по типу товара
        if ($request->has('product_type') && $request->product_type) {
            $query->where('product_type', 'ilike', "%{$request->product_type}%");
        }

        // Фильтрация по типу штрихкода
        if ($request->has('barcode_type') && $request->barcode_type) {
            $query->where('barcode_type', 'ilike', "%{$request->barcode_type}%");
        }

        // Фильтрация по штрихкоду
        if ($request->has('barcode') && $request->barcode) {
            $query->where('barcode', 'ilike', "%{$request->barcode}%");
        }

        // Фильтрация по налогу кассы
        if ($request->has('cash_register_tax') && $request->cash_register_tax) {
            $query->where('cash_register_tax', 'ilike', "%{$request->cash_register_tax}%");
        }

        // Фильтрация по типу кассы
        if ($request->has('cash_register_type') && $request->cash_register_type) {
            $query->where('cash_register_type', 'ilike', "%{$request->cash_register_type}%");
        }

        // Фильтрация по складу (warehouse_id)
        if ($request->has('warehouse_id') && $request->warehouse_id) {
            $query->whereExists(function($q) use ($request) {
                $q->select(DB::raw(1))
                  ->from('product_balances')
                  ->whereRaw('product_balances.product_id = products_sklad.id')
                  ->where('product_balances.warehouse_id', $request->warehouse_id);
            });
        }

        // Фильтрация по кастомным полям (fields->>'field_name')
        if ($request->all()) {
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'custom_') === 0 && $value !== null && $value !== '') {
                    $fieldName = substr($key, 7); // custom_fieldname
                    $query->whereRaw("fields->>? ILIKE ?", [$fieldName, "%$value%"]);
                }
            }
        }

        $products = $query->orderBy('created_at', 'desc')
                         ->paginate($perPage);

        // Преобразуем URL изображений и добавляем названия категорий
        $products->getCollection()->transform(function($product) {
            if ($product->images) {
                $product->images = $this->transformImageUrls($product->images);
            }
            
            // Добавляем названия категорий
            $product->category_name = $product->category_name;
            $product->subcategory_name = $product->subcategory_name;
            
            // Добавляем название склада
            $product->warehouse_name = $product->warehouse ? $product->warehouse->name : null;
            
            // Количество уже получено из JOIN запроса
            $product->quantity = (float) $product->total_quantity;
            
            // Берем цену из самого товара
            $product->price = (float) ($product->price ?? 0);
            
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Получить товар по ID с изображениями
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $product = ProductSklad::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['images' => function($query) {
                $query->orderBy('created_at', 'asc');
            }, 'categoryRelation', 'subcategoryRelation', 'warehouse', 'balances.warehouse'])
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Товар не найден'], 404);
        }

        // Получаем суммарное количество товара со всех складов из product_balances
        $totalQuantity = $product->balances->sum('quantity');
        $product->current_quantity = (float) $totalQuantity;
        
        // Получаем детализацию по складам
        $product->warehouse_balances = $product->balances->map(function($balance) {
            return [
                'warehouse_id' => $balance->warehouse_id,
                'quantity' => (float) $balance->quantity,
                'warehouse_name' => $balance->warehouse ? $balance->warehouse->name : null
            ];
        });

        // Преобразуем URL изображений и добавляем названия категорий
        if ($product->images && $product->images->count() > 0) {
            $product->images = $this->transformImageUrls($product->images);
        }
        
        // Добавляем названия категорий
        $product->category_name = $product->category_name;
        $product->subcategory_name = $product->subcategory_name;
        
        // Добавляем название склада
        $product->warehouse_name = $product->warehouse ? $product->warehouse->name : null;
        
        // Получаем последние данные из receipt_positions для количества и цены
        $latestReceiptPosition = \App\Models\ReceiptPosition::where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($latestReceiptPosition) {
            $product->quantity = (float) $latestReceiptPosition->quantity;
            $product->price = (float) $latestReceiptPosition->price;
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Удалить товар
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $product = ProductSklad::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Товар не найден'], 404);
        }

        DB::beginTransaction();
        try {
            // Удаляем товар (каскад удалит все связанные записи)
            $product->delete();
            DB::table('products')->where('id', $id)->delete();

            // Проверка: остались ли связанные записи (на всякий случай)
            $tables = [
                'product_images',
                'receipt_positions',
                'write_off_positions',
                'inventory_items',
                'product_transfer_positions',
                'product_operations',
                'product_balances',
            ];
            $leftovers = [];
            foreach ($tables as $table) {
                $count = DB::table($table)->where('product_id', $id)->count();
                if ($count > 0) {
                    $leftovers[$table] = $count;
                }
            }

            if (!empty($leftovers)) {
                Log::warning('Не все связанные записи удалены при каскадном удалении товара', [
                    'product_id' => $id,
                    'leftovers' => $leftovers
                ]);
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => 'Не удалось удалить все связанные записи (каскад не сработал)',
                    'details' => $leftovers
                ], 500);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Товар и все связанные данные успешно удалены'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при удалении товара', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Ошибка при удалении товара',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить остатки товаров при проведении оприходования
     */
    private function updateProductBalances($receipt)
    {
        $positions = $receipt->positions;
        
        foreach ($positions as $position) {
            // Используем product_id из позиции, если он есть, иначе ищем по артикулу или названию
            $product = null;
            if ($position->product_id) {
                $product = \App\Models\ProductSklad::find($position->product_id);
            } else {
                $product = \App\Models\ProductSklad::where('article', $position->article)
                    ->orWhere('name', $position->name)
                    ->first();
            }
            
            if ($product) {
                // Увеличиваем остаток на складе
                ProductBalance::incrementBalance(
                    $product->id,
                    $receipt->warehouse,
                    $position->quantity
                );
                
                // Создаем запись операции
                ProductOperation::createOperation([
                    'product_id' => $product->id,
                    'warehouse_id' => $receipt->warehouse,
                    'operation_type' => ProductOperation::TYPE_RECEIPT,
                    'quantity' => (int)$position->quantity,
                    'reference_type' => 'receipt',
                    'reference_id' => $receipt->id,
                    'notes' => "Оприходование №{$receipt->number}"
                ]);
            }
        }
    }

    /**
     * Массовый импорт товаров с автоматическим оприходованием
     */
    public function importWithReceipt(Request $request)
    {
        $user = $request->user();
        $warehouseId = $request->input('warehouse_id');
        $products = $request->input('products');

        if (!$warehouseId || !is_array($products) || count($products) === 0) {
            return response()->json(['success' => false, 'error' => 'warehouse_id и products обязательны'], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Массовое создание товаров с начальными остатками
            $createdProducts = [];
            foreach ($products as $prod) {
                $product = \App\Models\ProductSklad::create([
                    'user_id' => $user->id,
                    'name' => $prod['name'],
                    'description' => $prod['description'] ?? null,
                    'category' => $prod['category'] ?? null,
                    'subcategory' => $prod['subcategory'] ?? null,
                    'country' => $prod['country'] ?? null,
                    'supplier' => $prod['supplier'] ?? null,
                    'article' => $prod['article'] ?? null,
                    'code' => $prod['code'] ?? null,
                    'external_code' => $prod['external_code'] ?? null,
                    'unit' => $prod['unit'] ?? null,
                    'warehouse_id' => $warehouseId,
                    'weight' => $prod['weight'] ?? null,
                    'volume' => $prod['volume'] ?? null,
                    'vat' => $prod['vat'] ?? null,
                    'packing' => $prod['packing'] ?? null,
                    'accounting_type' => $prod['accounting_type'] ?? null,
                    'product_type' => $prod['product_type'] ?? null,
                    'barcode_type' => $prod['barcode_type'] ?? null,
                    'barcode' => $prod['barcode'] ?? null,
                    'cash_register_tax' => $prod['cash_register_tax'] ?? null,
                    'cash_register_type' => $prod['cash_register_type'] ?? null,
                    'start_count' => $prod['start_count'] ?? 0, // Используем start_count вместо quantity
                ]);
                $createdProducts[] = [
                    'model' => $product,
                    'input' => $prod,
                ];
            }

            // 2. Массовое создание/обновление остатков на основе начальных остатков
            foreach ($createdProducts as $item) {
                \App\Models\ProductBalance::updateOrCreate(
                    [
                        'product_id' => $item['model']->id,
                        'warehouse_id' => $warehouseId
                    ],
                    [
                        'quantity' => $item['input']['start_count'] ?? 0 // Используем start_count
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'created_products_count' => count($createdProducts)]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
} 