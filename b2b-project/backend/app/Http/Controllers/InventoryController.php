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
                'positions' => 'array',
                'positions.*.product_id' => 'required|exists:products_sklad,id',
                'positions.*.calculated_quantity' => 'required|numeric|min:0',
                'positions.*.actual_quantity' => 'required|numeric|min:0',
                'positions.*.notes' => 'nullable|string',
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

            // Создаем инвентаризацию
            $inventory = Inventory::create([
                'name' => $request->name,
                'description' => $request->description,
                'warehouse_id' => $request->warehouse,
                'status' => $request->status,
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
                        'notes' => $position['notes'] ?? null
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
            ])->find($id);

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
            $inventory = Inventory::find($id);

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
                'positions' => 'array',
                'positions.*.product_id' => 'required|exists:products_sklad,id',
                'positions.*.calculated_quantity' => 'required|numeric|min:0',
                'positions.*.actual_quantity' => 'required|numeric|min:0',
                'positions.*.notes' => 'nullable|string',
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

            // Обновляем инвентаризацию
            $inventory->update([
                'name' => $request->name,
                'description' => $request->description,
                'warehouse_id' => $request->warehouse,
                'status' => $request->status,
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
                        'notes' => $position['notes'] ?? null
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
            $inventory = Inventory::find($id);

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
} 