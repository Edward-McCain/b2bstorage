<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductSklad;
use App\Models\ProductImage;
use App\Models\ReceiptPosition;
use App\Models\Inventory;
use App\Models\InventoryItem;
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
     * Языковые переменные для сообщений
     */
    private function getLanguageMessages($userLanguage = 'ru')
    {
        $messages = [
            'ru' => [
                'user_not_authorized' => 'Пользователь не авторизован',
                'product_not_found' => 'Товар не найден',
                'product_not_found_or_access_denied' => 'Товар не найден или доступ запрещен',
                'image_not_found_or_access_denied' => 'Изображение не найдено или доступ запрещен',
                'category_not_exists' => 'Указанная категория не существует в ваших пользовательских категориях',
                'subcategory_not_exists' => 'Указанная подкатегория не существует в ваших пользовательских подкатегориях',
                'product_successfully_updated' => 'Товар успешно обновлен',
                'product_successfully_deleted' => 'Товар и все связанные данные успешно удалены',
                'error_deleting_product' => 'Ошибка при удалении товара',
                'product_balances_updated' => 'Остатки товара обновлены',
                'error_updating_product_balances' => 'Ошибка обновления остатков товара',
                'inventory_created_for_operation' => 'Создана инвентаризация для',
                'creating' => 'создания',
                'changing' => 'изменения',
                'initial_balance' => 'начального остатка',
                'receipt' => 'Оприходование',
                'automatic_inventory_for_product' => 'Автоматическая инвентаризация для товара',
                'automatic_inventory_for_products' => 'Автоматическая инвентаризация для товаров',
                'bulk_inventory_from' => 'Массовая инвентаризация от',
                'product_id' => 'Товар ID',
                'unknown_product' => 'Неизвестный товар',
                'unknown_user' => 'Неизвестный пользователь',
                'category_not_exists_in_user_categories' => 'не существует в пользовательских категориях, пропускаем',
                'subcategory_not_exists_in_user_subcategories' => 'не существует в пользовательских подкатегориях, пропускаем',
                'related_records_deleted_before_product_deletion' => 'Удаляем связанные записи перед удалением товара',
                'deleting_product_images' => 'Удаляем изображения товара',
                'deleting_product' => 'Удаляем сам товар',
                'automatic_inventory_created_for_product' => 'Создана автоматическая инвентаризация для товара',
                'unknown_warehouse' => 'Неизвестный склад',
                'unknown' => 'неизвестно',
                'creating_initial_balance' => 'Создание начального остатка',
                'changing_initial_balance' => 'Изменение начального остатка',
                'and_others' => 'и другие',
                'creating_bulk_inventory' => 'Создание массовой инвентаризации',
                'create_bulk_products_inventory_start' => 'CreateBulkProductsInventory: Начало обработки',
                'create_bulk_products_inventory_missing_product_id' => 'CreateBulkProductsInventory: Отсутствует product_id',
                'create_bulk_products_inventory_creating_item' => 'CreateBulkProductsInventory: Создание InventoryItem',
                'import_with_receipt_start' => 'ImportWithReceipt: Начало обработки',
                'import_with_receipt_creating_inventory' => 'ImportWithReceipt: Создание инвентаризации',
                'warehouse_id_and_products_required' => 'warehouse_id и products обязательны',
                'category_not_exists_in_user_categories_skipping' => "Категория '%s' не существует в пользовательских категориях, пропускаем",
                'category_not_exists_in_system_categories_skipping' => "Категория '%s' не существует в системных категориях, пропускаем",
                'subcategory_not_exists_in_user_subcategories_skipping' => "Подкатегория '%s' не существует в пользовательских подкатегориях, пропускаем",
                'subcategory_not_exists_in_system_subcategories_skipping' => "Подкатегория '%s' не существует в системных подкатегориях, пропускаем"
            ],
            'en' => [
                'user_not_authorized' => 'User not authorized',
                'product_not_found' => 'Product not found',
                'product_not_found_or_access_denied' => 'Product not found or access denied',
                'image_not_found_or_access_denied' => 'Image not found or access denied',
                'category_not_exists' => 'The specified category does not exist in your user categories',
                'subcategory_not_exists' => 'The specified subcategory does not exist in your user subcategories',
                'product_successfully_updated' => 'Product successfully updated',
                'product_successfully_deleted' => 'Product and all related data successfully deleted',
                'error_deleting_product' => 'Error deleting product',
                'product_balances_updated' => 'Product balances updated',
                'error_updating_product_balances' => 'Error updating product balances',
                'inventory_created_for_operation' => 'Inventory created for',
                'creating' => 'creating',
                'changing' => 'changing',
                'initial_balance' => 'initial balance',
                'receipt' => 'Receipt',
                'automatic_inventory_for_product' => 'Automatic inventory for product',
                'automatic_inventory_for_products' => 'Automatic inventory for products',
                'bulk_inventory_from' => 'Bulk inventory from',
                'product_id' => 'Product ID',
                'unknown_product' => 'Unknown product',
                'unknown_user' => 'Unknown user',
                'category_not_exists_in_user_categories' => 'does not exist in user categories, skipping',
                'subcategory_not_exists_in_user_subcategories' => 'does not exist in user subcategories, skipping',
                'related_records_deleted_before_product_deletion' => 'Deleting related records before product deletion',
                'deleting_product_images' => 'Deleting product images',
                'deleting_product' => 'Deleting the product',
                'automatic_inventory_created_for_product' => 'Automatic inventory created for product',
                'unknown_warehouse' => 'Unknown warehouse',
                'unknown' => 'unknown',
                'creating_initial_balance' => 'Creating initial balance',
                'changing_initial_balance' => 'Changing initial balance',
                'and_others' => 'and others',
                'creating_bulk_inventory' => 'Creating bulk inventory',
                'create_bulk_products_inventory_start' => 'CreateBulkProductsInventory: Starting processing',
                'create_bulk_products_inventory_missing_product_id' => 'CreateBulkProductsInventory: Missing product_id',
                'create_bulk_products_inventory_creating_item' => 'CreateBulkProductsInventory: Creating InventoryItem',
                'import_with_receipt_start' => 'ImportWithReceipt: Starting processing',
                'import_with_receipt_creating_inventory' => 'ImportWithReceipt: Creating inventory',
                'warehouse_id_and_products_required' => 'warehouse_id and products are required',
                'category_not_exists_in_user_categories_skipping' => "Category '%s' does not exist in user categories, skipping",
                'category_not_exists_in_system_categories_skipping' => "Category '%s' does not exist in system categories, skipping",
                'subcategory_not_exists_in_user_subcategories_skipping' => "Subcategory '%s' does not exist in user subcategories, skipping",
                'subcategory_not_exists_in_system_subcategories_skipping' => "Subcategory '%s' does not exist in system subcategories, skipping"
            ],
            'uz' => [
                'user_not_authorized' => 'Foydalanuvchi avtorizatsiya qilinmagan',
                'product_not_found' => 'Mahsulot topilmadi',
                'product_not_found_or_access_denied' => 'Mahsulot topilmadi yoki ruxsat rad etildi',
                'image_not_found_or_access_denied' => 'Rasm topilmadi yoki ruxsat rad etildi',
                'category_not_exists' => 'Ko\'rsatilgan kategoriya foydalanuvchi kategoriyalarida mavjud emas',
                'subcategory_not_exists' => 'Ko\'rsatilgan subkategoriya foydalanuvchi subkategoriyalarida mavjud emas',
                'product_successfully_updated' => 'Mahsulot muvaffaqiyatli yangilandi',
                'product_successfully_deleted' => 'Mahsulot va barcha bog\'liq ma\'lumotlar muvaffaqiyatli o\'chirildi',
                'error_deleting_product' => 'Mahsulotni o\'chirishda xatolik',
                'product_balances_updated' => 'Mahsulot qoldiqlari yangilandi',
                'error_updating_product_balances' => 'Mahsulot qoldiqlarini yangilashda xatolik',
                'inventory_created_for_operation' => 'Operatsiya uchun inventarizatsiya yaratildi',
                'creating' => 'yaratish',
                'changing' => 'o\'zgartirish',
                'initial_balance' => 'boshlang\'ich qoldiq',
                'receipt' => 'Kirim',
                'automatic_inventory_for_product' => 'Mahsulot uchun avtomatik inventarizatsiya',
                'automatic_inventory_for_products' => 'Mahsulotlar uchun avtomatik inventarizatsiya',
                'bulk_inventory_from' => 'Ommaviy inventarizatsiya',
                'product_id' => 'Mahsulot ID',
                'unknown_product' => 'Noma\'lum mahsulot',
                'unknown_user' => 'Noma\'lum foydalanuvchi',
                'category_not_exists_in_user_categories' => 'foydalanuvchi kategoriyalarida mavjud emas, o\'tkazib yuboriladi',
                'subcategory_not_exists_in_user_subcategories' => 'foydalanuvchi subkategoriyalarida mavjud emas, o\'tkazib yuboriladi',
                'related_records_deleted_before_product_deletion' => 'Mahsulotni o\'chirishdan oldin bog\'liq yozuvlarni o\'chirish',
                'deleting_product_images' => 'Mahsulot rasmlarini o\'chirish',
                'deleting_product' => 'Mahsulotni o\'chirish',
                'automatic_inventory_created_for_product' => 'Mahsulot uchun avtomatik inventarizatsiya yaratildi',
                'unknown_warehouse' => 'Noma\'lum ombor',
                'unknown' => 'noma\'lum',
                'creating_initial_balance' => 'Boshlang\'ich qoldiqni yaratish',
                'changing_initial_balance' => 'Boshlang\'ich qoldiqni o\'zgartirish',
                'and_others' => 'va boshqalar',
                'creating_bulk_inventory' => 'Ommaviy inventarizatsiyani yaratish',
                'create_bulk_products_inventory_start' => 'CreateBulkProductsInventory: Jarayonni boshlash',
                'create_bulk_products_inventory_missing_product_id' => 'CreateBulkProductsInventory: product_id yo\'q',
                'create_bulk_products_inventory_creating_item' => 'CreateBulkProductsInventory: InventoryItem yaratish',
                'import_with_receipt_start' => 'ImportWithReceipt: Jarayonni boshlash',
                'import_with_receipt_creating_inventory' => 'ImportWithReceipt: Inventarizatsiyani yaratish',
                'warehouse_id_and_products_required' => 'warehouse_id va products majburiy',
                'category_not_exists_in_user_categories_skipping' => "Kategoriya '%s' foydalanuvchi kategoriyalarida mavjud emas, o'tkazib yuboriladi",
                'category_not_exists_in_system_categories_skipping' => "Kategoriya '%s' tizim kategoriyalarida mavjud emas, o'tkazib yuboriladi",
                'subcategory_not_exists_in_user_subcategories_skipping' => "Subkategoriya '%s' foydalanuvchi subkategoriyalarida mavjud emas, o'tkazib yuboriladi",
                'subcategory_not_exists_in_system_subcategories_skipping' => "Subkategoriya '%s' tizim subkategoriyalarida mavjud emas, o'tkazib yuboriladi"
            ],
            'china' => [
                'user_not_authorized' => '用户未授权',
                'product_not_found' => '商品未找到',
                'product_not_found_or_access_denied' => '商品未找到或访问被拒绝',
                'image_not_found_or_access_denied' => '图片未找到或访问被拒绝',
                'category_not_exists' => '指定的类别在您的用户类别中不存在',
                'subcategory_not_exists' => '指定的子类别在您的用户子类别中不存在',
                'product_successfully_updated' => '商品更新成功',
                'product_successfully_deleted' => '商品和所有相关数据已成功删除',
                'error_deleting_product' => '删除商品时出错',
                'product_balances_updated' => '商品余额已更新',
                'error_updating_product_balances' => '更新商品余额时出错',
                'inventory_created_for_operation' => '为操作创建了库存',
                'creating' => '创建',
                'changing' => '更改',
                'initial_balance' => '初始余额',
                'receipt' => '收货',
                'automatic_inventory_for_product' => '商品的自动库存',
                'automatic_inventory_for_products' => '商品的自动库存',
                'bulk_inventory_from' => '批量库存',
                'product_id' => '商品ID',
                'unknown_product' => '未知商品',
                'unknown_user' => '未知用户',
                'category_not_exists_in_user_categories' => '在用户类别中不存在，跳过',
                'subcategory_not_exists_in_user_subcategories' => '在用户子类别中不存在，跳过',
                'related_records_deleted_before_product_deletion' => '删除商品前删除相关记录',
                'deleting_product_images' => '删除商品图片',
                'deleting_product' => '删除商品',
                'automatic_inventory_created_for_product' => '为商品创建了自动库存',
                'unknown_warehouse' => '未知仓库',
                'unknown' => '未知',
                'creating_initial_balance' => '创建初始余额',
                'changing_initial_balance' => '更改初始余额',
                'and_others' => '和其他',
                'creating_bulk_inventory' => '创建批量库存',
                'create_bulk_products_inventory_start' => 'CreateBulkProductsInventory: 开始处理',
                'create_bulk_products_inventory_missing_product_id' => 'CreateBulkProductsInventory: 缺少product_id',
                'create_bulk_products_inventory_creating_item' => 'CreateBulkProductsInventory: 创建InventoryItem',
                'import_with_receipt_start' => 'ImportWithReceipt: 开始处理',
                'import_with_receipt_creating_inventory' => 'ImportWithReceipt: 创建库存',
                'warehouse_id_and_products_required' => 'warehouse_id和products是必需的',
                'category_not_exists_in_user_categories_skipping' => "类别'%s'在用户类别中不存在，跳过",
                'category_not_exists_in_system_categories_skipping' => "类别'%s'在系统类别中不存在，跳过",
                'subcategory_not_exists_in_user_subcategories_skipping' => "子类别'%s'在用户子类别中不存在，跳过",
                'subcategory_not_exists_in_system_subcategories_skipping' => "子类别'%s'在系统子类别中不存在，跳过"
            ]
        ];

        return $messages[$userLanguage] ?? $messages['ru'];
    }
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
            $messages = $this->getLanguageMessages($user->language ?? 'ru');
            return response()->json(['error' => $messages['user_not_authorized']], 401);
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
            $messages = $this->getLanguageMessages($user->language ?? 'ru');
            return response()->json(['error' => $messages['user_not_authorized']], 401);
        }
        
        $product = ProductSklad::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$product) {
            $messages = $this->getLanguageMessages($user->language ?? 'ru');
            return response()->json(['error' => $messages['product_not_found_or_access_denied']], 404);
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
        
        // Сохраняем старое значение start_count для сравнения
        $oldStartCount = $product->start_count;
        
        // Проверяем настройки пользователя для категорий
        $productFieldsVisibility = $user->personal->product_fields_visibility ?? '{}';
        if (is_string($productFieldsVisibility)) {
            $productFieldsVisibility = json_decode($productFieldsVisibility, true) ?: [];
        }
        
        // Определяем, включены ли категории
        $categoriesEnabled = $productFieldsVisibility['category'] ?? true;
        
        // Получаем тип категорий пользователя
        $catsType = $user->cats_type ?? 'system';
        
        // Валидация данных
        $validationRules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
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
        ];
        
        // Добавляем валидацию категорий только если они включены
        if ($categoriesEnabled) {
            $validationRules['category_id'] = 'nullable|string';
            $validationRules['subcategory_id'] = 'nullable|string';
        }
        
        $request->validate($validationRules);

        // Валидация существования категорий если они включены
        if ($categoriesEnabled && $request->has('category_id') && $request->category_id) {
            if ($catsType === 'user') {
                // Проверяем существование в пользовательских категориях
                $categoryExists = DB::table('user_categories')
                    ->where('category_id', $request->category_id)
                    ->where('user_id', $user->id)
                    ->exists();
                    
                if (!$categoryExists) {
                    $messages = $this->getLanguageMessages($user->language ?? 'ru');
                    return response()->json([
                        'success' => false,
                        'message' => $messages['category_not_exists']
                    ], 422);
                }
            } else {
                // Проверяем существование в системных категориях
                $categoryExists = DB::table('categories')
                    ->where('category_id', $request->category_id)
                    ->exists();
                    
                if (!$categoryExists) {
                    $messages = $this->getLanguageMessages($user->language ?? 'ru');
                    return response()->json([
                        'success' => false,
                        'message' => $messages['category_not_exists']
                    ], 422);
                }
            }
        }

        // Валидация существования подкатегорий если они включены
        if ($categoriesEnabled && $request->has('subcategory_id') && $request->subcategory_id) {
            if ($catsType === 'user') {
                // Проверяем существование в пользовательских подкатегориях
                $subcategoryExists = DB::table('user_subcategories')
                    ->where('subcategory_id', $request->subcategory_id)
                    ->where('user_id', $user->id)
                    ->exists();
                    
                if (!$subcategoryExists) {
                    $messages = $this->getLanguageMessages($user->language ?? 'ru');
                    return response()->json([
                        'success' => false,
                        'message' => $messages['subcategory_not_exists']
                    ], 422);
                }
            } else {
                // Проверяем существование в системных подкатегориях
                $subcategoryExists = DB::table('subcategories')
                    ->where('subcategory_id', $request->subcategory_id)
                    ->exists();
                    
                if (!$subcategoryExists) {
                    $messages = $this->getLanguageMessages($user->language ?? 'ru');
                    return response()->json([
                        'success' => false,
                        'message' => $messages['subcategory_not_exists']
                    ], 422);
                }
            }
        }

        // Обновляем товар
        $updateData = [
            'name' => $request->name,
            'description' => $request->description,
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
        
        // Добавляем категории только если они включены
        if ($categoriesEnabled) {
            $updateData['category'] = $request->category_id;
            $updateData['subcategory'] = $request->subcategory_id;
        }
        
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

            $messages = $this->getLanguageMessages($user->language ?? 'ru');
            Log::info($messages['product_balances_updated'], [
                'product_id' => $product->id,
                'warehouse_id' => $warehouseId,
                'start_count' => $request->start_count
            ]);

            // Создаем инвентаризацию если изменился start_count
            $newStartCount = $request->start_count;
            if ($oldStartCount != $newStartCount) {
                // Определяем тип операции: создание или редактирование
                $operationType = $request->has('is_creation') && $request->is_creation ? 'create' : 'update';
                
                $this->createSingleProductInventory(
                    $product->id,
                    $warehouseId,
                    $newStartCount,
                    $operationType,
                    $oldStartCount,
                    $user->id
                );
                
            $operationText = $operationType === 'create' ? $messages['creating'] : $messages['changing'];
            Log::info($messages['inventory_created_for_operation'] . ' ' . $operationText . ' ' . $messages['initial_balance'], [
                    'product_id' => $product->id,
                    'old_start_count' => $oldStartCount,
                    'new_start_count' => $newStartCount,
                    'operation_type' => $operationType
                ]);
            }

        } catch (\Exception $e) {
            Log::error($messages['error_updating_product_balances'], [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $messages['product_successfully_updated'],
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
            $messages = $this->getLanguageMessages($user->language ?? 'ru');
            return response()->json(['error' => $messages['user_not_authorized']], 401);
        }
        
        $image = ProductImage::with('product')
            ->whereHas('product', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->find($id);
            
        if (!$image) {
            $messages = $this->getLanguageMessages($user->language ?? 'ru');
            return response()->json(['error' => $messages['image_not_found_or_access_denied']], 404);
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
            $messages = $this->getLanguageMessages($user->language ?? 'ru');
            return response()->json(['error' => $messages['user_not_authorized']], 401);
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
            $messages = $this->getLanguageMessages($user->language ?? 'ru');
            return response()->json(['error' => $messages['user_not_authorized']], 401);
        }

        $product = ProductSklad::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['images' => function($query) {
                $query->orderBy('created_at', 'asc');
            }, 'categoryRelation', 'subcategoryRelation', 'warehouse', 'balances.warehouse'])
            ->first();

        if (!$product) {
            $messages = $this->getLanguageMessages($user->language ?? 'ru');
            return response()->json(['error' => $messages['product_not_found']], 404);
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
        }
        
        // Получаем цену товара используя ту же логику, что и в ProductBalanceController
        $product->price = $this->getProductPrice($product->id);

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
            $messages = $this->getLanguageMessages($user->language ?? 'ru');
            return response()->json(['error' => $messages['user_not_authorized']], 401);
        }
        
        $product = ProductSklad::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$product) {
            $messages = $this->getLanguageMessages($user->language ?? 'ru');
            return response()->json(['error' => $messages['product_not_found']], 404);
        }

        DB::beginTransaction();
        try {
            // Проверяем, есть ли связанные записи, которые могут помешать удалению
            $relatedRecords = [];
            
            // Проверяем позиции списаний
            $writeOffPositionsCount = DB::table('write_off_positions')->where('product_id', $id)->count();
            if ($writeOffPositionsCount > 0) {
                $relatedRecords['write_off_positions'] = $writeOffPositionsCount;
            }
            
            // Проверяем позиции оприходований
            $receiptPositionsCount = DB::table('receipt_positions')->where('product_id', $id)->count();
            if ($receiptPositionsCount > 0) {
                $relatedRecords['receipt_positions'] = $receiptPositionsCount;
            }
            
            // Проверяем позиции инвентаризации
            $inventoryItemsCount = DB::table('inventory_items')->where('product_id', $id)->count();
            if ($inventoryItemsCount > 0) {
                $relatedRecords['inventory_items'] = $inventoryItemsCount;
            }
            
            // Проверяем позиции перемещений
            $transferPositionsCount = DB::table('product_transfer_positions')->where('product_id', $id)->count();
            if ($transferPositionsCount > 0) {
                $relatedRecords['product_transfer_positions'] = $transferPositionsCount;
            }
            
            // Проверяем операции
            $operationsCount = DB::table('product_operations')->where('product_id', $id)->count();
            if ($operationsCount > 0) {
                $relatedRecords['product_operations'] = $operationsCount;
            }
            
            // Проверяем остатки
            $balancesCount = DB::table('product_balances')->where('product_id', $id)->count();
            if ($balancesCount > 0) {
                $relatedRecords['product_balances'] = $balancesCount;
            }
            
            // Если есть связанные записи, удаляем их вручную
            if (!empty($relatedRecords)) {
                $messages = $this->getLanguageMessages($user->language ?? 'ru');
            Log::info($messages['related_records_deleted_before_product_deletion'], [
                    'product_id' => $id,
                    'related_records' => $relatedRecords
                ]);
                
                // Удаляем связанные записи в правильном порядке
                DB::table('write_off_positions')->where('product_id', $id)->delete();
                DB::table('receipt_positions')->where('product_id', $id)->delete();
                DB::table('inventory_items')->where('product_id', $id)->delete();
                DB::table('product_transfer_positions')->where('product_id', $id)->delete();
                DB::table('product_operations')->where('product_id', $id)->delete();
                DB::table('product_balances')->where('product_id', $id)->delete();
            }
            
            // Удаляем изображения товара
            $images = ProductImage::where('product_id', $id)->get();
            foreach ($images as $image) {
                if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
                    Storage::disk('public')->delete($image->image_url);
                }
            }
            DB::table('product_images')->where('product_id', $id)->delete();
            
            // Удаляем сам товар
            $product->delete();
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $messages['product_successfully_deleted']
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($messages['error_deleting_product'], [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => $messages['error_deleting_product'],
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить остатки товаров при проведении оприходования
     */
    private function updateProductBalances($receipt)
    {
        $user = Auth::user();
        $messages = $this->getLanguageMessages($user->language ?? 'ru');
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
                    'notes' => "{$messages['receipt']} №{$receipt->number}"
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
        $messages = $this->getLanguageMessages($user->language ?? 'ru');
        $warehouseId = $request->input('warehouse_id');
        $products = $request->input('products');

        Log::info($messages['import_with_receipt_start'], [
            'user_id' => $user ? $user->id : null,
            'warehouse_id' => $warehouseId,
            'products_count' => is_array($products) ? count($products) : 0
        ]);

        // Проверяем, есть ли warehouse_id на верхнем уровне или в первом продукте
        if (!$warehouseId && is_array($products) && count($products) > 0 && isset($products[0]['warehouse_id'])) {
            $warehouseId = $products[0]['warehouse_id'];
        }

        if (!$warehouseId || !is_array($products) || count($products) === 0) {
            return response()->json(['success' => false, 'error' => $messages['warehouse_id_and_products_required']], 422);
        }

        // Проверяем настройки пользователя для категорий
        $productFieldsVisibility = $user->personal->product_fields_visibility ?? '{}';
        if (is_string($productFieldsVisibility)) {
            $productFieldsVisibility = json_decode($productFieldsVisibility, true) ?: [];
        }
        
        // Определяем, включены ли категории
        $categoriesEnabled = $productFieldsVisibility['category'] ?? true;
        
        // Получаем тип категорий пользователя
        $catsType = $user->cats_type ?? 'system';

        DB::beginTransaction();
        try {
            // 1. Массовое создание товаров с начальными остатками
            $createdProducts = [];
            foreach ($products as $prod) {
                // Используем warehouse_id из продукта, если он есть, иначе используем общий warehouse_id
                $productWarehouseId = $prod['warehouse_id'] ?? $warehouseId;
                
                $productData = [
                    'user_id' => $user->id,
                    'name' => $prod['name'],
                    'description' => $prod['description'] ?? null,
                    'country' => $prod['country'] ?? null,
                    'supplier' => $prod['supplier'] ?? null,
                    'article' => $prod['article'] ?? null,
                    'code' => $prod['code'] ?? null,
                    'external_code' => $prod['external_code'] ?? null,
                    'unit' => $prod['unit'] ?? null,
                    'warehouse_id' => $productWarehouseId,
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
                    'price' => $prod['price'] ?? 0,
                    'start_count' => $prod['start_count'] ?? 0, // Используем start_count вместо quantity
                ];
                
                // Добавляем кастомные поля, если они есть
                if (isset($prod['fields']) && is_array($prod['fields']) && !empty($prod['fields'])) {
                    $productData['fields'] = json_encode($prod['fields']);
                }
                
                // Добавляем категории только если они включены
                if ($categoriesEnabled) {
                    $category = $prod['category'] ?? null;
                    $subcategory = $prod['subcategory'] ?? null;
                    
                    // Валидация существования категорий (делаем необязательными)
                    if ($category) {
                        if ($catsType === 'user') {
                            $categoryExists = DB::table('user_categories')
                                ->where('category_id', $category)
                                ->where('user_id', $user->id)
                                ->exists();
                                
                            if (!$categoryExists) {
                                // Логируем предупреждение, но не прерываем импорт
                                Log::warning(sprintf($messages['category_not_exists_in_user_categories_skipping'], $category), [
                                    'user_id' => $user->id,
                                    'category' => $category
                                ]);
                                $category = null; // Сбрасываем категорию
                            }
                        } else {
                            $categoryExists = DB::table('categories')
                                ->where('category_id', $category)
                                ->exists();
                                
                            if (!$categoryExists) {
                                // Логируем предупреждение, но не прерываем импорт
                                Log::warning(sprintf($messages['category_not_exists_in_system_categories_skipping'], $category), [
                                    'category' => $category
                                ]);
                                $category = null; // Сбрасываем категорию
                            }
                        }
                    }
                    
                    // Валидация существования подкатегорий (делаем необязательными)
                    if ($subcategory) {
                        if ($catsType === 'user') {
                            $subcategoryExists = DB::table('user_subcategories')
                                ->where('subcategory_id', $subcategory)
                                ->where('user_id', $user->id)
                                ->exists();
                                
                            if (!$subcategoryExists) {
                                // Логируем предупреждение, но не прерываем импорт
                                Log::warning(sprintf($messages['subcategory_not_exists_in_user_subcategories_skipping'], $subcategory), [
                                    'user_id' => $user->id,
                                    'subcategory' => $subcategory
                                ]);
                                $subcategory = null; // Сбрасываем подкатегорию
                            }
                        } else {
                            $subcategoryExists = DB::table('subcategories')
                                ->where('subcategory_id', $subcategory)
                                ->exists();
                                
                            if (!$subcategoryExists) {
                                // Логируем предупреждение, но не прерываем импорт
                                Log::warning(sprintf($messages['subcategory_not_exists_in_system_subcategories_skipping'], $subcategory), [
                                    'subcategory' => $subcategory
                                ]);
                                $subcategory = null; // Сбрасываем подкатегорию
                            }
                        }
                    }
                    
                    // Сохраняем category_id и subcategory_id как строки
                    $productData['category'] = $category;
                    $productData['subcategory'] = $subcategory;
                }
                
                $product = \App\Models\ProductSklad::create($productData);
                $createdProducts[] = [
                    'model' => $product,
                    'input' => $prod,
                ];
            }

            // 2. Массовое создание/обновление остатков на основе начальных остатков
            foreach ($createdProducts as $item) {
                // Используем warehouse_id из продукта, если он есть, иначе используем общий warehouse_id
                $productWarehouseId = $item['input']['warehouse_id'] ?? $warehouseId;
                
                \App\Models\ProductBalance::updateOrCreate(
                    [
                        'product_id' => $item['model']->id,
                        'warehouse_id' => $productWarehouseId
                    ],
                    [
                        'quantity' => $item['input']['start_count'] ?? 0 // Используем start_count
                    ]
                );
            }

            // 3. Создаем инвентаризацию для всех созданных товаров
            if (!empty($createdProducts)) {
                Log::info($messages['import_with_receipt_creating_inventory'], [
                    'created_products_count' => count($createdProducts),
                    'first_product_structure' => array_keys($createdProducts[0] ?? [])
                ]);
                // Используем warehouse_id из первого продукта, если он есть, иначе используем общий warehouse_id
                $firstProductWarehouseId = $createdProducts[0]['input']['warehouse_id'] ?? $warehouseId;
                $this->createBulkProductsInventory($createdProducts, $firstProductWarehouseId, $user->id);
            }

            DB::commit();
            return response()->json(['success' => true, 'created_products_count' => count($createdProducts)]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Создать инвентаризацию для одного товара
     * @param int $productId ID товара
     * @param int $warehouseId ID склада
     * @param int $quantity Количество
     * @param string $operationType Тип операции ('create' или 'update')
     * @param int|null $oldQuantity Старое количество (для обновления)
     * @param int|null $userId ID пользователя
     * @return int ID созданной инвентаризации
     */
    private function createSingleProductInventory($productId, $warehouseId, $quantity, $operationType, $oldQuantity = null, $userId = null)
    {
        $date = now()->format('d.m.Y');
        $inventoryName = $operationType === 'create' 
            ? "Добавление начальных остатков от {$date}"
            : "Изменение начальных остатков от {$date}";
        
        // Получаем название товара
        $product = ProductSklad::find($productId);
        $messages = $this->getLanguageMessages($user->language ?? 'ru');
        $productName = $product ? $product->name : "{$messages['product_id']}: {$productId}";
        
        // Используем переданный user_id или текущего пользователя
        $createdBy = $userId ?? Auth::id();
        
        $inventory = Inventory::create([
            'name' => $inventoryName,
            'description' => "{$messages['automatic_inventory_for_product']}: {$productName}",
            'warehouse_id' => $warehouseId,
            'status' => 'completed',
            'created_by' => $createdBy,
            'completed_at' => now()
        ]);
        
        $notes = $operationType === 'create'
            ? "{$messages['creating_initial_balance']} {$quantity}"
            : "{$messages['changing_initial_balance']} с {$oldQuantity} на {$quantity}";

        $calculatedQuantity = $operationType === 'update' ? $oldQuantity : 0;

        InventoryItem::create([
            'inventory_id' => $inventory->id,
            'product_id' => $productId,
            'calculated_quantity' => $calculatedQuantity,
            'actual_quantity' => $quantity,
            'notes' => $notes
        ]);
        
        Log::info($messages['automatic_inventory_created_for_product'], [
            'inventory_id' => $inventory->id,
            'product_id' => $productId,
            'product_name' => $productName,
            'warehouse_id' => $warehouseId,
            'operation_type' => $operationType,
            'quantity' => $quantity,
            'old_quantity' => $oldQuantity,
            'created_by' => $createdBy
        ]);
        
        return $inventory->id;
    }

    /**
     * Создать инвентаризацию для множества товаров
     * @param array $products Массив товаров с данными
     * @param int $warehouseId ID склада
     * @param int $userId ID пользователя
     * @return int ID созданной инвентаризации
     */
    private function createBulkProductsInventory($products, $warehouseId, $userId = null)
    {
        $user = Auth::user();
        $messages = $this->getLanguageMessages($user->language ?? 'ru');
        $createdBy = $userId ?? Auth::id();
        
        Log::info($messages['create_bulk_products_inventory_start'], [
            'created_by' => $createdBy,
            'warehouse_id' => $warehouseId,
            'products_count' => count($products),
            'first_product_structure' => array_keys($products[0] ?? [])
        ]);
        
        // Получаем названия товаров для описания
        $messages = $this->getLanguageMessages($user->language ?? 'ru');
        $productNames = [];
        foreach ($products as $product) {
            // Проверяем структуру данных
            if (isset($product['model']) && isset($product['input'])) {
                // Новая структура из importWithReceipt
                $productNames[] = $product['input']['name'] ?? "{$messages['product_id']}: {$product['model']->id}";
            } else {
                // Старая структура
                $productNames[] = $product['name'] ?? "{$messages['product_id']}: " . ($product['id'] ?? $messages['unknown']);
            }
        }
        
        // Ограничиваем список до 5 товаров для описания
        $productNamesList = implode(', ', array_slice($productNames, 0, 5));
        if (count($productNames) > 5) {
            $productNamesList .= ' ' . $messages['and_others'];
        }
        
        Log::info($messages['creating_bulk_inventory'], [
            'created_by' => $createdBy,
            'product_names' => $productNamesList
        ]);
        
        $date = now()->format('d.m.Y');
        $inventoryName = "{$messages['bulk_inventory_from']} {$date}";
        
        $inventory = Inventory::create([
            'name' => $inventoryName,
            'user_id' => $createdBy,
            'warehouse_id' => $warehouseId,
            'status' => 'completed',
            'description' => "{$messages['automatic_inventory_for_products']}: {$productNamesList}",
            'created_by' => $createdBy
        ]);
        
        foreach ($products as $product) {
            // Проверяем структуру данных
            if (isset($product['model']) && isset($product['input'])) {
                // Новая структура из importWithReceipt
                $productId = $product['model']->id;
                $quantity = $product['input']['start_count'] ?? 0;
            } else {
                // Старая структура
                $productId = $product['id'] ?? null;
                $quantity = $product['quantity'] ?? 0;
            }
            
            // Проверяем, что productId существует
            if (!$productId) {
                Log::error($messages['create_bulk_products_inventory_missing_product_id'], [
                    'product' => $product
                ]);
                continue;
            }
            
            Log::info($messages['create_bulk_products_inventory_creating_item'], [
                'inventory_id' => $inventory->id,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
            
            InventoryItem::create([
                'inventory_id' => $inventory->id,
                'product_id' => $productId,
                'calculated_quantity' => 0,
                'actual_quantity' => $quantity,
                'notes' => "{$messages['creating_initial_balance']} {$quantity}"
            ]);
        }
        
        return $inventory;
    }

    /**
     * Получить цену товара
     */
    private function getProductPrice($productId)
    {
        // Сначала пытаемся получить цену из самого товара
        $product = \App\Models\ProductSklad::find($productId);
        
        // Если в products_sklad есть цена > 0, берем её как основную
        if ($product && $product->price > 0) {
            return (float) $product->price;
        }

        // Если в products_sklad цена = 0 или null, ищем в последнем оприходовании
        $lastReceiptPosition = \App\Models\ReceiptPosition::where('product_id', $productId)
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastReceiptPosition ? (float) $lastReceiptPosition->price : 0;
    }

    /**
     * Получить логи операций с товарами
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductOperations(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            $messages = $this->getLanguageMessages($user->language ?? 'ru');
            return response()->json(['error' => $messages['user_not_authorized']], 401);
        }
        
        $messages = $this->getLanguageMessages($user->language ?? 'ru');

        $query = ProductOperation::with(['product', 'warehouse', 'createdByUser'])
            ->whereHas('product', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });

        // Фильтр по типу операции
        if ($request->has('operation_type') && $request->operation_type) {
            $query->where('operation_type', $request->operation_type);
        }

        // Фильтр по складу
        if ($request->has('warehouse') && $request->warehouse) {
            $query->where('warehouse_id', $request->warehouse);
        }

        // Фильтр по дате от
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Фильтр по дате до
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Сортировка по дате создания (новые сначала)
        $query->orderBy('created_at', 'desc');

        $operations = $query->get();

        // Формируем ответ с дополнительными данными
        $formattedOperations = $operations->map(function ($operation) use ($messages) {
            return [
                'id' => $operation->id,
                'product_id' => $operation->product_id,
                'product_name' => $operation->product->name ?? $messages['unknown_product'],
                'product_code' => $operation->product->code ?? '',
                'warehouse_id' => $operation->warehouse_id,
                'warehouse_name' => $operation->warehouse->name ?? $messages['unknown_warehouse'],
                'operation_type' => $operation->operation_type,
                'quantity' => $operation->quantity,
                'reference_type' => $operation->reference_type,
                'reference_id' => $operation->reference_id,
                'notes' => $operation->notes,
                'created_by' => $operation->created_by,
                'created_by_name' => $operation->createdByUser->first_name ?? $messages['unknown_user'],
                'created_at' => $operation->created_at,
                'updated_at' => $operation->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedOperations
        ]);
    }
} 