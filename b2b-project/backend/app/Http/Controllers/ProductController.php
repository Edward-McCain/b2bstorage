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
        
        $product = ProductSklad::findOrFail($id);
        
        // Валидация данных
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable',
            'subcategory' => 'nullable',
            'country' => 'nullable',
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
            'category' => $request->category ? (string) $request->category : null,
            'subcategory' => $request->subcategory ? (string) $request->subcategory : null,
            'country' => $request->country ? (string) $request->country : null,
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
            'product_type' => $request->product_type,
            'barcode_type' => $request->barcode_type,
            'barcode' => $request->barcode,
            'cash_register_tax' => $request->cash_register_tax,
            'cash_register_type' => $request->cash_register_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Товар успешно сохранен',
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
} 