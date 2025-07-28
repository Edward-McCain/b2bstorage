<?php

namespace App\Http\Controllers;

use App\Models\ProductTransfer;
use App\Models\ProductTransferPosition;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\ProductBalance;
use App\Models\ProductOperation;
use App\Models\ProductSklad;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProductTransferController extends Controller
{
    /**
     * Получить список перемещений (GET - для простого получения списка)
     */
    public function index(Request $request): JsonResponse
    {
        $query = ProductTransfer::with([
            'fromWarehouse',
            'toWarehouse',
            'createdByUser',
            'completedByUser',
            'positions.product'
        ])->select('*');

        // Фильтруем по текущему пользователю
        $query->where('created_by', Auth::id());

        $transfers = $query->orderBy('created_at', 'desc')->paginate(20);

        // Добавляем отладочную информацию
        foreach ($transfers->items() as $transfer) {
            $transfer->load(['positions.product', 'createdByUser', 'completedByUser']);
            
            // Принудительно вычисляем аксессоры
            $transfer->total_items = $transfer->getTotalItemsAttribute();
            $transfer->actual_total_items = $transfer->getActualTotalItemsAttribute();
            $transfer->status_text = $transfer->getStatusTextAttribute();
        }

        // Преобразуем данные для фронтенда
        $transfersData = $transfers->toArray();
        foreach ($transfersData['data'] as &$transfer) {
            // Добавляем статус текстом
            $transfer['status_text'] = ProductTransfer::getStatuses()[$transfer['status']] ?? $transfer['status'];
            
            // Добавляем общее количество единиц товаров
            $transfer['total_items'] = collect($transfer['positions'] ?? [])->sum('quantity');
            $transfer['actual_total_items'] = collect($transfer['positions'] ?? [])->sum('actual_quantity') ?? 0;
            
            // Добавляем информацию о пользователях
            if (isset($transfer['created_by_user'])) {
                $transfer['created_by_user']['name'] = $transfer['created_by_user']['first_name'] . ' ' . $transfer['created_by_user']['last_name'];
            }
            if (isset($transfer['completed_by_user'])) {
                $transfer['completed_by_user']['name'] = $transfer['completed_by_user']['first_name'] . ' ' . $transfer['completed_by_user']['last_name'];
            }
        }

        return response()->json([
            'transfers' => $transfersData,
            'statuses' => ProductTransfer::getStatuses()
        ]);
    }

    /**
     * Фильтрация перемещений (POST)
     */
    public function filter(Request $request): JsonResponse
    {
        // Логируем входящие параметры
        Log::info('Filter request', [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'warehouse_id' => $request->warehouse_id,
            'page' => $request->page,
        ]);

        
        $query = ProductTransfer::with([
            'fromWarehouse',
            'toWarehouse',
            'createdByUser',
            'completedByUser',
            'positions.product'
        ])->select('*');

        // Фильтруем по текущему пользователю
        $query->where('created_by', Auth::id());

        // Применяем фильтр по складу только если warehouse_id не пустой
        if (!empty($request->warehouse_id)) {
            $query->byWarehouse($request->warehouse_id);
        }

        if ($request->has('date_from') && $request->has('date_to') && $request->date_from && $request->date_to) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        // Получаем номер страницы из запроса
        $page = $request->input('page', 1);
        $transfers = $query->orderBy('created_at', 'desc')->paginate(20, ['*'], 'page', $page);

        // Добавляем отладочную информацию
        foreach ($transfers->items() as $transfer) {
            $transfer->load(['positions.product', 'createdByUser', 'completedByUser']);
            
            // Принудительно вычисляем аксессоры
            $transfer->total_items = $transfer->getTotalItemsAttribute();
            $transfer->actual_total_items = $transfer->getActualTotalItemsAttribute();
            $transfer->status_text = $transfer->getStatusTextAttribute();
        }

        // Преобразуем данные для фронтенда
        $transfersData = $transfers->toArray();
        foreach ($transfersData['data'] as &$transfer) {
            // Добавляем статус текстом
            $transfer['status_text'] = ProductTransfer::getStatuses()[$transfer['status']] ?? $transfer['status'];
            
            // Добавляем общее количество единиц товаров
            $transfer['total_items'] = collect($transfer['positions'] ?? [])->sum('quantity');
            $transfer['actual_total_items'] = collect($transfer['positions'] ?? [])->sum('actual_quantity') ?? 0;
            
            // Добавляем информацию о пользователях
            if (isset($transfer['created_by_user'])) {
                $transfer['created_by_user']['name'] = $transfer['created_by_user']['first_name'] . ' ' . $transfer['created_by_user']['last_name'];
            }
            if (isset($transfer['completed_by_user'])) {
                $transfer['completed_by_user']['name'] = $transfer['completed_by_user']['first_name'] . ' ' . $transfer['completed_by_user']['last_name'];
            }
        }

        // Логируем SQL-запрос
        Log::info('Filter SQL', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);

        return response()->json([
            'transfers' => $transfersData,
            'statuses' => ProductTransfer::getStatuses()
        ]);
    }

    /**
     * Получить перемещение по ID
     */
    public function show(int $id): JsonResponse
    {
        $transfer = ProductTransfer::with([
            'fromWarehouse',
            'toWarehouse',
            'createdByUser',
            'completedByUser',
            'positions.product'
        ])->where('created_by', Auth::id())->findOrFail($id);

        return response()->json($transfer);
    }

    /**
     * Создать новое перемещение
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'positions' => 'required|array|min:1',
            'positions.*.product_id' => 'required|exists:products_sklad,id',
            'positions.*.quantity' => 'required|integer|min:1',
            'positions.*.notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Проверяем достаточность товаров на складе-отправителе
            foreach ($request->positions as $position) {
                $fromBalance = ProductBalance::where('product_id', $position['product_id'])
                    ->where('warehouse_id', $request->from_warehouse_id)
                    ->first();

                $availableQuantity = $fromBalance ? $fromBalance->quantity : 0;

                if ($availableQuantity < $position['quantity']) {
                    throw ValidationException::withMessages([
                        'positions' => "Недостаточно товара с ID {$position['product_id']} на складе-отправителе. Доступно: {$availableQuantity}, требуется: {$position['quantity']}"
                    ]);
                }
            }

            $transfer = ProductTransfer::create([
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id' => $request->to_warehouse_id,
                'transfer_date' => $request->transfer_date,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'status' => ProductTransfer::STATUS_COMPLETED // Сразу выполняем перемещение
            ]);

            // Создаем позиции и обновляем остатки
            foreach ($request->positions as $position) {
                $quantity = $position['quantity'];
                
                // Создаем позицию перемещения
                ProductTransferPosition::create([
                    'transfer_id' => $transfer->id,
                    'product_id' => $position['product_id'],
                    'quantity' => $quantity,
                    'actual_quantity' => $quantity, // Фактическое количество равно запланированному
                    'notes' => $position['notes'] ?? null
                ]);

                // Обновляем остатки на складе-отправителе
                $fromBalance = ProductBalance::where('product_id', $position['product_id'])
                    ->where('warehouse_id', $request->from_warehouse_id)
                    ->first();

                if ($fromBalance) {
                    $newQuantity = $fromBalance->quantity - $quantity;
                    
                    if ($newQuantity <= 0) {
                        // Если товара не осталось, удаляем запись
                        $fromBalance->delete();
                    } else {
                        // Обновляем количество
                        $fromBalance->update(['quantity' => $newQuantity]);
                    }
                }

                // Обновляем остатки на складе-получателе
                $toBalance = ProductBalance::firstOrCreate([
                    'product_id' => $position['product_id'],
                    'warehouse_id' => $request->to_warehouse_id
                ], ['quantity' => 0]);

                $toBalance->increment('quantity', $quantity);

                // Записываем операции
                ProductOperation::createOperation([
                    'product_id' => $position['product_id'],
                    'warehouse_id' => $request->from_warehouse_id,
                    'operation_type' => 'transfer_out',
                    'quantity' => -$quantity,
                    'reference_type' => 'product_transfer',
                    'reference_id' => $transfer->id,
                    'notes' => "Перемещение в склад: " . Warehouse::find($request->to_warehouse_id)->name
                ]);

                ProductOperation::createOperation([
                    'product_id' => $position['product_id'],
                    'warehouse_id' => $request->to_warehouse_id,
                    'operation_type' => 'transfer_in',
                    'quantity' => $quantity,
                    'reference_type' => 'product_transfer',
                    'reference_id' => $transfer->id,
                    'notes' => "Перемещение со склада: " . Warehouse::find($request->from_warehouse_id)->name
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Перемещение выполнено успешно',
                'transfer' => $transfer->load(['positions.product', 'fromWarehouse', 'toWarehouse'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Обновить перемещение
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $transfer = ProductTransfer::where('created_by', Auth::id())->findOrFail($id);

        if ($transfer->status !== ProductTransfer::STATUS_DRAFT) {
            throw ValidationException::withMessages([
                'status' => 'Можно редактировать только черновики'
            ]);
        }

        $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'positions' => 'required|array|min:1',
            'positions.*.product_id' => 'required|exists:products,id',
            'positions.*.quantity' => 'required|integer|min:1',
            'positions.*.notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $transfer->update([
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id' => $request->to_warehouse_id,
                'transfer_date' => $request->transfer_date,
                'notes' => $request->notes
            ]);

            // Удаляем старые позиции и создаем новые
            $transfer->positions()->delete();

            foreach ($request->positions as $position) {
                ProductTransferPosition::create([
                    'transfer_id' => $transfer->id,
                    'product_id' => $position['product_id'],
                    'quantity' => $position['quantity'],
                    'notes' => $position['notes'] ?? null
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Перемещение обновлено успешно',
                'transfer' => $transfer->load(['positions.product', 'fromWarehouse', 'toWarehouse'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Подтвердить перемещение
     */
    public function confirm(int $id): JsonResponse
    {
        $transfer = ProductTransfer::with(['positions.product'])->where('created_by', Auth::id())->findOrFail($id);

        if (!$transfer->canBeConfirmed()) {
            throw ValidationException::withMessages([
                'status' => 'Можно подтверждать только черновики'
            ]);
        }

        // Проверяем достаточность товаров на складе-отправителе
        foreach ($transfer->positions as $position) {
            $balance = ProductBalance::where('product_id', $position->product_id)
                ->where('warehouse_id', $transfer->from_warehouse_id)
                ->first();

            $availableQuantity = $balance ? $balance->quantity : 0;

            if ($availableQuantity < $position->quantity) {
                throw ValidationException::withMessages([
                    'positions' => "Недостаточно товара '{$position->product->name}' на складе-отправителе. Доступно: {$availableQuantity}, требуется: {$position->quantity}"
                ]);
            }
        }

        $transfer->update(['status' => ProductTransfer::STATUS_CONFIRMED]);

        return response()->json([
            'message' => 'Перемещение подтверждено',
            'transfer' => $transfer->load(['positions.product', 'fromWarehouse', 'toWarehouse'])
        ]);
    }

    /**
     * Выполнить перемещение
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        $transfer = ProductTransfer::with(['positions.product'])->where('created_by', Auth::id())->findOrFail($id);

        if (!$transfer->canBeCompleted()) {
            throw ValidationException::withMessages([
                'status' => 'Можно выполнять только подтвержденные перемещения'
            ]);
        }

        $request->validate([
            'positions' => 'required|array',
            'positions.*.id' => 'required|exists:product_transfer_positions,id',
            'positions.*.actual_quantity' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Обновляем позиции с фактическими количествами
            foreach ($request->positions as $positionData) {
                $position = ProductTransferPosition::find($positionData['id']);
                $position->update(['actual_quantity' => $positionData['actual_quantity']]);
            }

            // Выполняем перемещение товаров
            foreach ($transfer->positions as $position) {
                $fromWarehouseId = $transfer->from_warehouse_id;
                $toWarehouseId = $transfer->to_warehouse_id;
                $quantity = $position->actual_quantity;

                if ($quantity > 0) {
                    // Уменьшаем количество на складе-отправителе
                    $fromBalance = ProductBalance::firstOrCreate([
                        'product_id' => $position->product_id,
                        'warehouse_id' => $fromWarehouseId
                    ], ['quantity' => 0]);

                    $fromBalance->decrement('quantity', $quantity);

                    // Увеличиваем количество на складе-получателе
                    $toBalance = ProductBalance::firstOrCreate([
                        'product_id' => $position->product_id,
                        'warehouse_id' => $toWarehouseId
                    ], ['quantity' => 0]);

                    $toBalance->increment('quantity', $quantity);

                    // Записываем операции
                    ProductOperation::createOperation([
                        'product_id' => $position->product_id,
                        'warehouse_id' => $fromWarehouseId,
                        'operation_type' => 'transfer_out',
                        'quantity' => (int)-$quantity,
                        'reference_type' => 'product_transfer',
                        'reference_id' => $transfer->id,
                        'notes' => "Перемещение в склад: {$transfer->toWarehouse->name}"
                    ]);

                    ProductOperation::createOperation([
                        'product_id' => $position->product_id,
                        'warehouse_id' => $toWarehouseId,
                        'operation_type' => 'transfer_in',
                        'quantity' => (int)$quantity,
                        'reference_type' => 'product_transfer',
                        'reference_id' => $transfer->id,
                        'notes' => "Перемещение со склада: {$transfer->fromWarehouse->name}"
                    ]);
                }
            }

            $transfer->update([
                'status' => ProductTransfer::STATUS_COMPLETED,
                'completed_at' => now(),
                'completed_by' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Перемещение выполнено успешно',
                'transfer' => $transfer->load(['positions.product', 'fromWarehouse', 'toWarehouse'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Отменить перемещение
     */
    public function cancel(int $id): JsonResponse
    {
        $transfer = ProductTransfer::where('created_by', Auth::id())->findOrFail($id);

        if (!$transfer->canBeCancelled()) {
            throw ValidationException::withMessages([
                'status' => 'Нельзя отменить это перемещение'
            ]);
        }

        $transfer->update(['status' => ProductTransfer::STATUS_CANCELLED]);

        return response()->json([
            'message' => 'Перемещение отменено',
            'transfer' => $transfer->load(['positions.product', 'fromWarehouse', 'toWarehouse'])
        ]);
    }

    /**
     * Удалить перемещение
     */
    public function destroy(int $id): JsonResponse
    {
        $transfer = ProductTransfer::where('created_by', Auth::id())->findOrFail($id);

        if ($transfer->status !== ProductTransfer::STATUS_DRAFT) {
            throw ValidationException::withMessages([
                'status' => 'Можно удалять только черновики'
            ]);
        }

        $transfer->delete();

        return response()->json([
            'message' => 'Перемещение удалено'
        ]);
    }

    /**
     * Получить доступные товары для перемещения
     */
    public function getAvailableProducts(Request $request): JsonResponse
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id'
        ]);

        $user = Auth::user();
        
        Log::info('getAvailableProducts: Запрос товаров для склада', [
            'warehouse_id' => $request->warehouse_id,
            'user_id' => $user->id
        ]);
        
        // Получаем товары с остатками на указанном складе
        $balances = ProductBalance::where('warehouse_id', $request->warehouse_id)
            ->where('quantity', '>', 0)
            ->whereHas('product', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        $products = [];
        
        foreach ($balances as $balance) {
            // Получаем данные товара из products_sklad
            $product = \App\Models\ProductSklad::find($balance->product_id);
            
            if ($product) {
                $products[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'article' => $product->article,
                    'description' => $product->description,
                    'category' => $product->category,
                    'subcategory' => $product->subcategory,
                    'unit' => $product->unit,
                    'warehouse_quantity' => $balance->quantity, // Количество на складе
                    'selected_quantity' => 0 // Количество для перемещения (по умолчанию 0)
                ];
            }
        }
        
        Log::info('getAvailableProducts: Найдено товаров', [
            'warehouse_id' => $request->warehouse_id,
            'products_count' => count($products)
        ]);

        return response()->json($products);
    }

    /**
     * Получить все товары для выбора
     */
    public function getAllProducts(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $search = $request->get('search', '');
        
        $query = \App\Models\ProductSklad::where('user_id', $user->id);

        // Поиск по названию или артикулу
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('article', 'ilike', "%{$search}%");
            });
        }

        $products = $query->select('id', 'name', 'article', 'description', 'category', 'subcategory', 'unit')
                         ->orderBy('name')
                         ->limit(100)
                         ->get()
                         ->map(function ($product) {
                             return [
                                 'id' => $product->id,
                                 'name' => $product->name,
                                 'article' => $product->article,
                                 'description' => $product->description,
                                 'category' => $product->category,
                                 'subcategory' => $product->subcategory,
                                 'unit' => $product->unit
                             ];
                         });

        return response()->json($products);
    }
} 