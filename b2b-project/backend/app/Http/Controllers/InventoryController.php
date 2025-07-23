<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\InventoryFile;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    /**
     * Получить список инвентаризаций
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Inventory::with(['warehouse', 'createdBy', 'items.product', 'files']);

            // Фильтруем по текущему пользователю
            $query->where('created_by', Auth::id());

            // Фильтры
            if ($request->filled('name')) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }

            if ($request->filled('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
            }

            if ($request->filled('warehouse')) {
                $query->where('warehouse_id', $request->warehouse);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $inventories = $query->orderBy('created_at', 'desc')->get();

            // Добавляем дополнительную информацию
            $inventories->each(function ($inventory) {
                $inventory->items_count = $inventory->items->count();
                $inventory->discrepancies_count = $inventory->items->where('excess_shortage', '!=', 'normal')->count();
                $inventory->warehouse_name = $inventory->warehouse->name ?? null;
                $inventory->warehouse_address = $inventory->warehouse->address ?? null;
                $inventory->created_by_name = $inventory->createdBy->first_name ?? null;
                
                // Обрабатываем товары
                $inventory->items->each(function ($item) {
                    if ($item->product) {
                        $item->product_name = $item->product->name ?? null;
                        $item->product_sku = $item->product->sku ?? null;
                    }
                    
                    // Вычисляем разницу
                    $item->difference_quantity = $item->actual_quantity - $item->calculated_quantity;
                    
                    // Определяем статус избытка/недостачи
                    if ($item->difference_quantity > 0) {
                        $item->excess_shortage = 'excess';
                    } elseif ($item->difference_quantity < 0) {
                        $item->excess_shortage = 'shortage';
                    } else {
                        $item->excess_shortage = 'normal';
                    }
                });
                
                // Обрабатываем файлы
                $inventory->files->each(function ($file) {
                    // Добавляем file_url для фронтенда
                    $file->file_url = $file->file_path;
                });
                
                // Удаляем полную модель пользователя, оставляем только имя
                unset($inventory->createdBy);
            });

            return response()->json([
                'success' => true,
                'data' => $inventories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка инвентаризаций: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Создать новую инвентаризацию
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'warehouse' => 'required|exists:warehouses,id',
                'status' => 'required|in:draft,in_progress,completed,cancelled',
                'auto_create_operations' => 'nullable|boolean',
                'positions' => 'array',
                'positions.*.product_id' => 'required|exists:products_sklad,id',
                'positions.*.calculated_quantity' => 'required|integer|min:0',
                'positions.*.actual_quantity' => 'required|integer|min:0',
                'positions.*.notes' => 'nullable|string',
                'positions.*.photo' => 'nullable|string|max:500',
                'inventory_files' => 'array',
                'inventory_files.*.filename' => 'nullable|string|max:255',
                'inventory_files.*.file_url' => 'nullable|string|max:500',
                'inventory_files.*.file_size' => 'nullable|numeric|min:0',
                'inventory_files.*.uploaded_by' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Определяем статус инвентаризации
            $status = $request->status;
            if ($request->auto_create_operations === true) {
                $status = 'completed'; // При автоматических операциях статус завершен
            }

            // Создаем инвентаризацию
            $inventory = Inventory::create([
                'name' => $request->name,
                'description' => $request->description,
                'warehouse_id' => $request->warehouse,
                'status' => $status,
                'created_by' => Auth::id(),
                'notes' => $request->notes
            ]);

            // Добавляем товары
            if ($request->has('positions')) {
                foreach ($request->positions as $position) {
                    InventoryItem::create([
                        'inventory_id' => $inventory->id,
                        'product_id' => $position['product_id'],
                        'calculated_quantity' => $position['calculated_quantity'],
                        'actual_quantity' => $position['actual_quantity'],
                        'notes' => $position['notes'] ?? null,
                        'photo' => $position['photo'] ?? null
                    ]);
                }
            }

            // Привязываем файлы
            if ($request->has('inventory_files') && is_array($request->inventory_files)) {
                foreach ($request->inventory_files as $fileData) {
                    InventoryFile::create([
                        'inventory_id' => $inventory->id,
                        'filename' => $fileData['filename'] ?? '',
                        'original_filename' => $fileData['filename'] ?? '',
                        'file_path' => $fileData['file_url'] ?? '',
                        'file_size' => $fileData['file_size'] ?? 0,
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            // Создаем автоматические операции если включено
            if ($request->auto_create_operations) {
                \Illuminate\Support\Facades\Log::info('Начинаем создание автоматических операций (store)', [
                    'inventory_id' => $inventory->id,
                    'auto_create_operations' => $request->auto_create_operations
                ]);
                
                // Временно отключено для отладки зависания
                // $this->createAutoOperations($inventory);
                
                \Illuminate\Support\Facades\Log::info('Автоматические операции отключены для отладки (store)');
                
                // Если были бы созданы автоматические операции, меняем статус
                $inventory->update(['status' => 'completed']);
            }

            DB::commit();

            // Загружаем связанные данные
            $inventory->load(['warehouse', 'createdBy', 'items.product', 'files']);

            // Обрабатываем файлы
            $inventory->files->each(function ($file) {
                $file->file_url = $file->file_path;
            });
            
            // Удаляем полную модель пользователя, оставляем только имя
            unset($inventory->createdBy);

            return response()->json([
                'success' => true,
                'message' => 'Инвентаризация создана успешно',
                'data' => $inventory
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании инвентаризации: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить инвентаризацию по ID
     */
    public function show($id): JsonResponse
    {
        try {
            $inventory = Inventory::with([
                'warehouse', 
                'createdBy', 
                'items.product', 
                'files'
            ])->where('created_by', Auth::id())->find($id);

            if (!$inventory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Инвентаризация не найдена'
                ], 404);
            }

            // Добавляем дополнительную информацию
            $inventory->items_count = $inventory->items->count();
            $inventory->discrepancies_count = $inventory->items->where('excess_shortage', '!=', 'normal')->count();
            $inventory->warehouse_name = $inventory->warehouse->name ?? null;
            $inventory->warehouse_address = $inventory->warehouse->address ?? null;
            $inventory->created_by_name = $inventory->createdBy->first_name ?? null;
            
            // Обрабатываем товары
            $inventory->items->each(function ($item) {
                if ($item->product) {
                    $item->product_name = $item->product->name ?? null;
                    $item->product_sku = $item->product->article ?? null;
                }
                
                // Вычисляем разницу
                $item->difference_quantity = $item->actual_quantity - $item->calculated_quantity;
                
                // Определяем статус избытка/недостачи
                if ($item->difference_quantity > 0) {
                    $item->excess_shortage = 'excess';
                } elseif ($item->difference_quantity < 0) {
                    $item->excess_shortage = 'shortage';
                } else {
                    $item->excess_shortage = 'normal';
                }
            });
            
            // Обрабатываем файлы
            $inventory->files->each(function ($file) {
                $file->file_url = $file->file_path;
            });
            
            // Удаляем полную модель пользователя, оставляем только имя
            unset($inventory->createdBy);

            return response()->json([
                'success' => true,
                'data' => $inventory
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении инвентаризации: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить инвентаризацию
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $inventory = Inventory::where('created_by', Auth::id())->find($id);

            if (!$inventory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Инвентаризация не найдена'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'warehouse' => 'required|exists:warehouses,id',
                'status' => 'required|in:draft,in_progress,completed,cancelled',
                'auto_create_operations' => 'nullable|boolean',
                'positions' => 'array',
                'positions.*.product_id' => 'required|exists:products_sklad,id',
                'positions.*.calculated_quantity' => 'required|integer|min:0',
                'positions.*.actual_quantity' => 'required|integer|min:0',
                'positions.*.notes' => 'nullable|string',
                'positions.*.photo' => 'nullable|string|max:500',
                'inventory_files' => 'array',
                'inventory_files.*.filename' => 'nullable|string|max:255',
                'inventory_files.*.file_url' => 'nullable|string|max:500',
                'inventory_files.*.file_size' => 'nullable|numeric|min:0',
                'inventory_files.*.uploaded_by' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Определяем статус инвентаризации
            $status = $request->status;
            if ($request->auto_create_operations === true) {
                $status = 'completed'; // При автоматических операциях статус завершен
            }

            // Обновляем инвентаризацию
            $inventory->update([
                'name' => $request->name,
                'description' => $request->description,
                'warehouse_id' => $request->warehouse,
                'status' => $status,
                'notes' => $request->notes
            ]);

            // Обновляем товары
            if ($request->has('positions')) {
                // Удаляем старые позиции
                $inventory->items()->delete();

                // Добавляем новые позиции
                foreach ($request->positions as $position) {
                    InventoryItem::create([
                        'inventory_id' => $inventory->id,
                        'product_id' => $position['product_id'],
                        'calculated_quantity' => $position['calculated_quantity'],
                        'actual_quantity' => $position['actual_quantity'],
                        'notes' => $position['notes'] ?? null,
                        'photo' => $position['photo'] ?? null
                    ]);
                }
            }

            // Обновляем файлы
            if ($request->has('inventory_files') && is_array($request->inventory_files)) {
                // Удаляем старые файлы
                InventoryFile::where('inventory_id', $inventory->id)->delete();
                // Добавляем новые файлы
                foreach ($request->inventory_files as $fileData) {
                    InventoryFile::create([
                        'inventory_id' => $inventory->id,
                        'filename' => $fileData['filename'] ?? '',
                        'original_filename' => $fileData['filename'] ?? '',
                        'file_path' => $fileData['file_url'] ?? '',
                        'file_size' => $fileData['file_size'] ?? 0,
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            // Создаем автоматические операции если включено
            if ($request->auto_create_operations) {
                \Illuminate\Support\Facades\Log::info('Начинаем создание автоматических операций (update)', [
                    'inventory_id' => $inventory->id,
                    'auto_create_operations' => $request->auto_create_operations
                ]);
                
                // Временно отключено для отладки зависания
                // $this->createAutoOperations($inventory);
                
                \Illuminate\Support\Facades\Log::info('Автоматические операции отключены для отладки (update)');
                
                // Если были бы созданы автоматические операции, меняем статус
                $inventory->update(['status' => 'completed']);
            }

            DB::commit();

            // Загружаем связанные данные
            $inventory->load(['warehouse', 'createdBy', 'items.product', 'files']);

            // Добавляем дополнительную информацию
            $inventory->items_count = $inventory->items->count();
            $inventory->discrepancies_count = $inventory->items->where('excess_shortage', '!=', 'normal')->count();
            $inventory->warehouse_name = $inventory->warehouse->name ?? null;
            $inventory->warehouse_address = $inventory->warehouse->address ?? null;
            $inventory->created_by_name = $inventory->createdBy->first_name ?? null;
            
            // Обрабатываем товары
            $inventory->items->each(function ($item) {
                if ($item->product) {
                    $item->product_name = $item->product->name ?? null;
                    $item->product_sku = $item->product->article ?? null;
                }
                
                // Вычисляем разницу
                $item->difference_quantity = $item->actual_quantity - $item->calculated_quantity;
                
                // Определяем статус избытка/недостачи
                if ($item->difference_quantity > 0) {
                    $item->excess_shortage = 'excess';
                } elseif ($item->difference_quantity < 0) {
                    $item->excess_shortage = 'shortage';
                } else {
                    $item->excess_shortage = 'normal';
                }
            });
            
            // Обрабатываем файлы
            $inventory->files->each(function ($file) {
                if ($file->file_path) {
                    if (str_starts_with($file->file_path, 'http')) {
                        $file->file_url = $file->file_path;
                    } else {
                        $file->file_url = request()->getSchemeAndHttpHost() . $file->file_path;
                    }
                } else {
                    $file->file_url = '';
                }
            });
            
            // Удаляем полную модель пользователя, оставляем только имя
            unset($inventory->createdBy);

            return response()->json([
                'success' => true,
                'message' => 'Инвентаризация обновлена успешно',
                'data' => $inventory
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении инвентаризации: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить инвентаризацию
     */
    public function destroy($id): JsonResponse
    {
        try {
            $inventory = Inventory::where('created_by', Auth::id())->find($id);

            if (!$inventory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Инвентаризация не найдена'
                ], 404);
            }

            DB::beginTransaction();

            // Удаляем связанные данные
            $inventory->items()->delete();
            $inventory->files()->delete();
            $inventory->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Инвентаризация удалена успешно'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении инвентаризации: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Экспорт инвентаризации в Excel
     */
    // Метод getWarehouseProducts удален - используем api/transfers/available-products

    /**
     * Рассчитать остатки для товара на складе
     */
    public function calculateBalances(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'warehouse_id' => 'required|exists:warehouses,id',
                'product_ids' => 'nullable|array',
                'product_ids.*' => 'exists:products_sklad,id'
            ]);

            $warehouseId = $request->warehouse_id;
            $productIds = $request->product_ids ?? [];

            // Получаем все товары на складе, если не указаны конкретные
            if (empty($productIds)) {
                $productIds = DB::table('products_sklad')
                    ->where('user_id', Auth::id())
                    ->pluck('id')
                    ->toArray();
            }

            $balances = [];

            foreach ($productIds as $productId) {
                // Получаем оприходования (приходы)
                $receipts = DB::table('receipt_positions as rp')
                    ->join('receipts as r', 'rp.receipt_id', '=', 'r.id')
                    ->where('r.warehouse', $warehouseId)
                    ->where('r.user_id', Auth::id())
                    ->where('rp.product_id', $productId)
                    ->sum('rp.quantity');

                // Получаем списания (расходы)
                $writeOffs = DB::table('write_off_positions as wop')
                    ->join('write_offs as wo', 'wop.write_off_id', '=', 'wo.id')
                    ->where('wo.warehouse', $warehouseId)
                    ->where('wo.user_id', Auth::id())
                    ->where('wop.product_id', $productId)
                    ->sum('wop.quantity');

                // Получаем перемещения (приходы с других складов)
                $transfersIn = DB::table('product_transfer_positions as ptp')
                    ->join('product_transfers as pt', 'ptp.transfer_id', '=', 'pt.id')
                    ->where('pt.to_warehouse_id', $warehouseId)
                    ->where('pt.created_by', Auth::id())
                    ->where('ptp.product_id', $productId)
                    ->sum('ptp.actual_quantity');

                // Получаем перемещения (расходы на другие склады)
                $transfersOut = DB::table('product_transfer_positions as ptp')
                    ->join('product_transfers as pt', 'ptp.transfer_id', '=', 'pt.id')
                    ->where('pt.from_warehouse_id', $warehouseId)
                    ->where('pt.created_by', Auth::id())
                    ->where('ptp.product_id', $productId)
                    ->sum('ptp.actual_quantity');

                // Рассчитываем остаток: Оприходования + Перемещения приходы - Списания - Перемещения расходы
                $calculatedBalance = $receipts + $transfersIn - $writeOffs - $transfersOut;

                // Получаем информацию о товаре
                $product = DB::table('products_sklad')
                    ->where('id', $productId)
                    ->first();

                $balances[] = [
                    'product_id' => $productId,
                    'product_name' => $product->name ?? 'Неизвестный товар',
                    'product_article' => $product->article ?? '',
                    'calculated_balance' => $calculatedBalance,
                    'receipts' => $receipts,
                    'write_offs' => $writeOffs,
                    'transfers_in' => $transfersIn,
                    'transfers_out' => $transfersOut
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $balances,
                'warehouse_id' => $warehouseId
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при расчете остатков: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export($id): JsonResponse
    {
        try {
            $inventory = Inventory::with([
                'warehouse', 
                'createdBy', 
                'items.product'
            ])->find($id);

            if (!$inventory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Инвентаризация не найдена'
                ], 404);
            }

            // Здесь должна быть логика экспорта в Excel
            // Пока возвращаем заглушку
            $filename = 'inventory_' . $id . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            $downloadUrl = '/storage/exports/' . $filename;

            return response()->json([
                'success' => true,
                'message' => 'Файл подготовлен для скачивания',
                'data' => [
                    'download_url' => $downloadUrl,
                    'filename' => $filename
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при экспорте: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Создать автоматические операции по расхождениям инвентаризации
     */
    private function createAutoOperations(Inventory $inventory): void
    {
        \Illuminate\Support\Facades\Log::info('createAutoOperations запущен', [
            'inventory_id' => $inventory->id,
            'warehouse_id' => $inventory->warehouse_id,
            'items_count' => $inventory->items()->count()
        ]);

        // Проверяем, не были ли уже созданы автоматические операции для этой инвентаризации
        $receiptExists = \App\Models\Receipt::where('number', "ИНВ-ИЗБ-{$inventory->id}-" . date('dmY'))->exists();
        $writeOffExists = \App\Models\WriteOff::where('number', "ИНВ-СПИ-{$inventory->id}-" . date('dmY'))->exists();
        
        if ($receiptExists || $writeOffExists) {
            // Операции уже были созданы, выходим
            \Illuminate\Support\Facades\Log::info('Автоматические операции уже существуют, выходим', [
                'inventory_id' => $inventory->id
            ]);
            return;
        }

        // Загружаем товары с расчетом разниц
        $inventory->load('items.product');
        
        $excessItems = [];
        $shortageItems = [];
        
        foreach ($inventory->items as $item) {
            $difference = $item->actual_quantity - $item->calculated_quantity;
            
            if ($difference > 0) {
                $excessItems[] = $item;
            } elseif ($difference < 0) {
                $shortageItems[] = $item;
            }
        }
        
        \Illuminate\Support\Facades\Log::info('Обработка расхождений', [
            'inventory_id' => $inventory->id,
            'excess_items_count' => count($excessItems),
            'shortage_items_count' => count($shortageItems)
        ]);

        // Создаем оприходование для избытков
        if (!empty($excessItems)) {
            \Illuminate\Support\Facades\Log::info('Создание оприходования для избытков');
            $this->createReceiptForExcess($inventory, $excessItems);
        }
        
        // Создаем списание для недостач
        if (!empty($shortageItems)) {
            \Illuminate\Support\Facades\Log::info('Создание списания для недостач');
            $this->createWriteOffForShortage($inventory, $shortageItems);
        }

        \Illuminate\Support\Facades\Log::info('createAutoOperations завершен', [
            'inventory_id' => $inventory->id
        ]);
    }

    /**
     * Создать автоматическое оприходование для избытков
     */
    private function createReceiptForExcess(Inventory $inventory, array $excessItems): void
    {
        $receiptData = [
            'number' => "ИНВ-ИЗБ-{$inventory->id}-" . date('dmY'),
            'date' => now()->format('Y-m-d H:i:s'),
            'organization' => 'Автоматическое оприходование по инвентаризации: ' . $inventory->name,
            'project' => 'Инвентаризация',
            'warehouse' => $inventory->warehouse_id,
            'status' => 'posted',
            'is_posted' => true,
            'comment' => "Автоматическое оприходование по инвентаризации №{$inventory->id}",
            'positions' => []
        ];
        
        $totalAmount = 0;
        
        foreach ($excessItems as $item) {
            $excessQuantity = $item->actual_quantity - $item->calculated_quantity;
            $price = $item->product->price ?? 0;
            $amount = $excessQuantity * $price;
            $totalAmount += $amount;
            
            $receiptData['positions'][] = [
                'product_id' => $item->product_id,
                'name' => $item->product->name ?? '',
                'article' => $item->product->article ?? '',
                'quantity' => $excessQuantity,
                'price' => $price,
                'amount' => $amount,
                'reason' => "Избыток по инвентаризации №{$inventory->id}"
            ];
        }
        
        // Создаем оприходование
        $receipt = \App\Models\Receipt::create([
            'number' => $receiptData['number'],
            'date' => $receiptData['date'],
            'status' => $receiptData['status'],
            'is_posted' => $receiptData['is_posted'],
            'organization' => $receiptData['organization'],
            'project' => $receiptData['project'],
            'warehouse' => $receiptData['warehouse'],
            'comment' => $receiptData['comment'],
            'total' => $totalAmount,
            'user_id' => Auth::id(),
            'created_by' => Auth::user()->first_name ?? 'Система'
        ]);
        
        // Создаем позиции оприходования
        foreach ($receiptData['positions'] as $position) {
            \App\Models\ReceiptPosition::create([
                'receipt_id' => $receipt->id,
                'product_id' => $position['product_id'],
                'name' => $position['name'],
                'article' => $position['article'],
                'quantity' => $position['quantity'],
                'price' => $position['price'],
                'amount' => $position['amount'],
                'reason' => $position['reason']
            ]);
        }
        
        // Обновляем остатки товаров
        // Временно отключено для отладки зависания
        // $this->updateProductBalancesForReceipt($receipt);
    }

    /**
     * Создать автоматическое списание для недостач
     */
    private function createWriteOffForShortage(Inventory $inventory, array $shortageItems): void
    {
        $writeOffData = [
            'number' => "ИНВ-СПИ-{$inventory->id}-" . date('dmY'),
            'date' => now()->format('Y-m-d H:i:s'),
            'organization' => 'Автоматическое списание по инвентаризации: ' . $inventory->name,
            'project' => 'Инвентаризация',
            'warehouse' => $inventory->warehouse_id,
            'status' => 'posted',
            'is_posted' => true,
            'comment' => "Автоматическое списание по инвентаризации №{$inventory->id}",
            'positions' => []
        ];
        
        $totalAmount = 0;
        
        foreach ($shortageItems as $item) {
            $shortageQuantity = abs($item->actual_quantity - $item->calculated_quantity);
            $price = $item->product->price ?? 0;
            $amount = $shortageQuantity * $price;
            $totalAmount += $amount;
            
            $writeOffData['positions'][] = [
                'product_id' => $item->product_id,
                'name' => $item->product->name ?? '',
                'article' => $item->product->article ?? '',
                'quantity' => $shortageQuantity,
                'price' => $price,
                'amount' => $amount,
                'reason' => "Недостача по инвентаризации №{$inventory->id}"
            ];
        }
        
        // Создаем списание
        $writeOff = \App\Models\WriteOff::create([
            'number' => $writeOffData['number'],
            'date' => $writeOffData['date'],
            'organization' => $writeOffData['organization'],
            'project' => $writeOffData['project'],
            'warehouse' => $writeOffData['warehouse'],
            'status' => $writeOffData['status'],
            'is_posted' => $writeOffData['is_posted'],
            'comment' => $writeOffData['comment'],
            'total' => $totalAmount,
            'user_id' => Auth::id(),
            'created_by' => Auth::user()->first_name ?? 'Система'
        ]);
        
        // Создаем позиции списания
        foreach ($writeOffData['positions'] as $position) {
            \App\Models\WriteOffPosition::create([
                'write_off_id' => $writeOff->id,
                'product_id' => $position['product_id'],
                'name' => $position['name'],
                'article' => $position['article'],
                'quantity' => $position['quantity'],
                'price' => $position['price'],
                'amount' => $position['amount'],
                'reason' => $position['reason']
            ]);
        }
        
        // Обновляем остатки товаров
        // Временно отключено для отладки зависания
        // $this->updateProductBalancesForWriteOff($writeOff);
    }

    /**
     * Обновить остатки товаров для оприходования
     */
    private function updateProductBalancesForReceipt(\App\Models\Receipt $receipt): void
    {
        $positions = $receipt->positions;
        
        foreach ($positions as $position) {
            if ($position->product_id) {
                $product = \App\Models\ProductSklad::find($position->product_id);
                
                if ($product) {
                    // Увеличиваем остаток на складе
                    \App\Models\ProductBalance::incrementBalance(
                        $product->id,
                        $receipt->warehouse,
                        $position->quantity
                    );
                    
                    // Создаем запись операции
                    \App\Models\ProductOperation::createOperation([
                        'product_id' => $product->id,
                        'warehouse_id' => $receipt->warehouse,
                        'operation_type' => \App\Models\ProductOperation::TYPE_RECEIPT,
                        'quantity' => (int)$position->quantity,
                        'reference_type' => 'inventory_receipt',
                        'reference_id' => $receipt->id,
                        'notes' => "Автоматическое оприходование по инвентаризации №{$receipt->number}"
                    ]);
                }
            }
        }
    }

    /**
     * Обновить остатки товаров для списания
     */
    private function updateProductBalancesForWriteOff(\App\Models\WriteOff $writeOff): void
    {
        $positions = $writeOff->positions;
        
        foreach ($positions as $position) {
            if ($position->product_id) {
                $product = \App\Models\ProductSklad::find($position->product_id);
                
                if ($product) {
                    // Уменьшаем остаток на складе
                    \App\Models\ProductBalance::decrementBalance(
                        $product->id,
                        $writeOff->warehouse,
                        $position->quantity
                    );
                    
                    // Создаем запись операции
                    \App\Models\ProductOperation::createOperation([
                        'product_id' => $product->id,
                        'warehouse_id' => $writeOff->warehouse,
                        'operation_type' => \App\Models\ProductOperation::TYPE_WRITE_OFF,
                        'quantity' => -(int)$position->quantity, // Отрицательное количество для списания
                        'reference_type' => 'inventory_write_off',
                        'reference_id' => $writeOff->id,
                        'notes' => "Автоматическое списание по инвентаризации №{$writeOff->number}"
                    ]);
                }
            }
        }
    }
} 