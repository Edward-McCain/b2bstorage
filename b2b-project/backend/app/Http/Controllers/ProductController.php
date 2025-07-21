<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductSklad;
use App\Models\ProductImage;
use App\Models\ReceiptPosition;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;
use App\Models\ProductBalance;
use App\Models\ProductOperation;
use Illuminate\Support\Facades\DB;
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
        
        $product = ProductSklad::create([
            'user_id' => $user->id,
            'name' => $request->name,
        ]);
        
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
        $product = ProductSklad::findOrFail($id);
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
        return response()->json(['image' => $img], 201);
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
            'quantity' => 'required|numeric|min:0',
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
        ]);

        // Обновляем товар
        $product->update([
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
            'quantity' => $request->quantity,
            'price' => $request->price,
        ]);

        // Если изменились количество или цена, обновляем последнюю запись в receipt_positions
        $latestReceiptPosition = \App\Models\ReceiptPosition::where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestReceiptPosition) {
            $updated = false;
            
            // Обновляем количество, если оно изменилось
            if ($latestReceiptPosition->quantity != $request->quantity) {
                $latestReceiptPosition->quantity = $request->quantity;
                $updated = true;
            }
            
            // Обновляем цену, если она изменилась
            if ($latestReceiptPosition->price != $request->price) {
                $latestReceiptPosition->price = $request->price;
                $updated = true;
            }
            
            // Пересчитываем сумму
            if ($updated) {
                $latestReceiptPosition->amount = $request->quantity * $request->price;
                $latestReceiptPosition->save();
            }
        }

        // Создаем автоматическое оприходование
        try {
            $receipt = \App\Models\Receipt::create([
                'number' => 'AUTO-' . time(),
                'date' => now(),
                'status' => 'posted',
                'is_posted' => true,
                'organization' => 'Автоматическое оприходование',
                'warehouse' => $request->warehouse_id,
                'user_id' => $user->id,
                'created_by' => $user->user_name ?? $user->first_name ?? 'System'
            ]);

            // Создаем позицию оприходования
            $price = $request->price ?? 0;
            $amount = $price * $request->quantity;
            
            \App\Models\ReceiptPosition::create([
                'receipt_id' => $receipt->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'article' => $product->article,
                'code' => $product->code,
                'quantity' => $request->quantity,
                'price' => $price,
                'amount' => $amount,
                'balance' => $request->quantity
            ]);

            // Обновляем общую сумму оприходования
            $receipt->update(['total' => $amount]);

            Log::info('Автоматическое оприходование создано', [
                'product_id' => $product->id,
                'receipt_id' => $receipt->id,
                'quantity' => $request->quantity,
                'warehouse_id' => $request->warehouse_id,
                'price' => $price,
                'amount' => $amount
            ]);

            // Обновляем остатки товаров при проведении оприходования
            $this->updateProductBalances($receipt);

        } catch (\Exception $e) {
            Log::error('Ошибка создания автоматического оприходования', [
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
        $image = ProductImage::findOrFail($id);
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
                'warehouse',
                'receiptPositions'
            ]);

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
            $query->whereHas('receiptPositions', function($q) use ($request) {
                $q->where('quantity', $request->quantity);
            });
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
            
            // Получаем последние данные из receipt_positions для количества и цены
            $latestReceiptPosition = \App\Models\ReceiptPosition::where('product_id', $product->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($latestReceiptPosition) {
                $product->quantity = (float) $latestReceiptPosition->quantity;
                $product->price = (float) $latestReceiptPosition->price;
            }
            
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
            }, 'categoryRelation', 'subcategoryRelation', 'warehouse'])
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Товар не найден'], 404);
        }

        // Получаем последние данные из receipt_positions для этого товара
        $latestReceiptPosition = \App\Models\ReceiptPosition::where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        // Если есть данные из receipt_positions, используем их для дополнения информации о товаре
        if ($latestReceiptPosition) {
            // Дополняем данные товара информацией из последней позиции оприходования
            $product->latest_quantity = $latestReceiptPosition->quantity;
            $product->latest_price = $latestReceiptPosition->price;
            $product->latest_amount = $latestReceiptPosition->amount;
            $product->latest_balance = $latestReceiptPosition->balance;
            $product->latest_code = $latestReceiptPosition->code;
            $product->latest_article = $latestReceiptPosition->article;
            $product->latest_barcode = $latestReceiptPosition->barcode;
            $product->latest_country = $latestReceiptPosition->country;
            $product->latest_gtd = $latestReceiptPosition->gtd;
            $product->latest_rnpt = $latestReceiptPosition->rnpt;
            $product->latest_reason = $latestReceiptPosition->reason;
        }

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

        // Удаляем изображения товара
        $product->images()->delete();
        
        // Удаляем товар
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Товар успешно удален'
        ]);
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
} 