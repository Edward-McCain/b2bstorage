<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Receipt;
use App\Models\ReceiptPosition;
use App\Models\ProductSklad;

// Подключаемся к базе данных
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Тест сохранения product_id в receipt_positions ===\n\n";

try {
    // Проверяем, есть ли товары в базе
    $products = ProductSklad::take(3)->get();
    echo "Найдено товаров: " . $products->count() . "\n";
    
    if ($products->count() > 0) {
        $product = $products->first();
        echo "Используем товар: {$product->name} (ID: {$product->id})\n\n";
        
        // Создаем тестовое оприходование
        $receipt = Receipt::create([
            'number' => 'TEST-' . time(),
            'date' => now(),
            'status' => 'draft',
            'is_posted' => false,
            'organization' => 'Тестовая организация',
            'warehouse' => 1, // Предполагаем, что склад с ID 1 существует
            'user_id' => 1, // Предполагаем, что пользователь с ID 1 существует
            'created_by' => 'Test User'
        ]);
        
        echo "Создано оприходование ID: {$receipt->id}\n";
        
        // Создаем позицию с product_id
        $position = ReceiptPosition::create([
            'receipt_id' => $receipt->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'article' => $product->article,
            'quantity' => 10,
            'price' => 100.50,
            'amount' => 1005.00,
            'balance' => 0
        ]);
        
        echo "Создана позиция ID: {$position->id}\n";
        echo "product_id в позиции: {$position->product_id}\n\n";
        
        // Проверяем, что product_id сохранился
        $savedPosition = ReceiptPosition::find($position->id);
        echo "Проверка сохранения:\n";
        echo "- product_id в базе: " . ($savedPosition->product_id ?? 'NULL') . "\n";
        echo "- Ожидаемый product_id: {$product->id}\n";
        
        if ($savedPosition->product_id == $product->id) {
            echo "✅ product_id успешно сохранен!\n";
        } else {
            echo "❌ product_id НЕ сохранен!\n";
        }
        
        // Очищаем тестовые данные
        $receipt->positions()->delete();
        $receipt->delete();
        echo "\nТестовые данные удалены.\n";
        
    } else {
        echo "❌ Нет товаров в базе данных для тестирования!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Тест завершен ===\n"; 