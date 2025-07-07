<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductSklad;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

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
        $path = 'products_img/' . $filename;
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
        $images = ProductImage::where('product_id', $id)->orderBy('id')->get();
        
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
        ]);

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
                'subcategoryRelation'
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

        // Фильтрация по поставщику
        if ($request->has('supplier') && $request->supplier) {
            $query->where('supplier', 'ilike', "%{$request->supplier}%");
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
            ->with(['images', 'categoryRelation', 'subcategoryRelation'])
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Товар не найден'], 404);
        }

        // Преобразуем URL изображений и добавляем названия категорий
        if ($product->images) {
            $product->images = $this->transformImageUrls($product->images);
        }
        
        // Добавляем названия категорий
        $product->category_name = $product->category_name;
        $product->subcategory_name = $product->subcategory_name;

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
} 