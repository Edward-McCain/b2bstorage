<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\User;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем существующие данные
        $warehouses = Warehouse::all();
        $products = Product::all();
        $users = User::all();

        if ($warehouses->isEmpty() || $products->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Недостаточно данных для создания инвентаризаций. Создайте склады, товары и пользователей.');
            return;
        }

        $statuses = ['draft', 'in_progress', 'completed', 'cancelled'];
        $inventoryNames = [
            'Инвентаризация склада А',
            'Проверка остатков',
            'Годовая инвентаризация',
            'Внеплановая проверка',
            'Инвентаризация после ремонта'
        ];

        for ($i = 0; $i < 5; $i++) {
            $inventory = Inventory::create([
                'name' => $inventoryNames[$i] ?? "Инвентаризация #" . ($i + 1),
                'description' => 'Описание инвентаризации ' . ($i + 1),
                'warehouse_id' => $warehouses->random()->id,
                'status' => $statuses[array_rand($statuses)],
                'created_by' => $users->random()->id,
                'completed_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                'notes' => rand(0, 1) ? 'Дополнительные примечания к инвентаризации' : null
            ]);

            // Добавляем товары в инвентаризацию
            $randomProducts = $products->random(rand(3, 8));
            
            foreach ($randomProducts as $product) {
                $calculatedQuantity = rand(10, 100);
                $actualQuantity = $calculatedQuantity + rand(-20, 20); // Может быть больше или меньше

                InventoryItem::create([
                    'inventory_id' => $inventory->id,
                    'product_id' => $product->id,
                    'calculated_quantity' => $calculatedQuantity,
                    'actual_quantity' => max(0, $actualQuantity), // Не может быть отрицательным
                    'notes' => rand(0, 1) ? 'Примечание к товару' : null
                ]);
            }
        }

        $this->command->info('Создано 5 тестовых инвентаризаций с товарами.');
    }
} 