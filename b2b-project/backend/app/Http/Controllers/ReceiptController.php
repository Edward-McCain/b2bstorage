<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\ReceiptPosition;
use App\Models\ProductBalance;
use App\Models\ProductOperation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB as DBFacade;
use Illuminate\Support\Facades\Log;

class ReceiptController extends Controller
{
    /**
     * Получить список оприходований
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $query = DB::table('receipts as r')
                ->leftJoin('warehouses as w', 'r.warehouse', '=', 'w.id')
                ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
                ->select(
                    'r.id', 'r.number', 'r.date', 'r.organization',
                    'r.warehouse as warehouse_id',
                    'w.name as warehouse_name',
                    'w.address as warehouse_address',
                    'r.status', 'r.total', 'r.created_by', 'r.user_id', 'r.is_posted',
                    'r.comment', 'r.overhead_costs', 'r.project', 'r.created_at', 'r.updated_at',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as user_full_name")
                )
                ->where('r.user_id', $user->id);

            // Применяем фильтры
            if ($request->has('number') && !empty($request->number)) {
                $query->where('r.number', 'like', '%' . $request->number . '%');
            }

            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->where('r.date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->where('r.date', '<=', $request->date_to . ' 23:59:59');
            }

            if ($request->has('warehouse') && !empty($request->warehouse)) {
                $query->where('r.warehouse', $request->warehouse);
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('r.status', $request->status);
            }

            $receipts = $query->orderBy('r.created_at', 'desc')->paginate(20);

            $data = collect($receipts->items())->map(function($receipt) {
                return [
                    'id' => $receipt->id,
                    'number' => $receipt->number,
                    'date' => $receipt->date,
                    'organization' => $receipt->organization,
                    'warehouse_id' => $receipt->warehouse_id,
                    'warehouse_name' => $receipt->warehouse_name ?? '',
                    'warehouse_address' => $receipt->warehouse_address ?? '',
                    'status' => $receipt->status,
                    'total' => $receipt->total,
                    'created_by' => $receipt->user_full_name ?? $receipt->created_by,
                    'user_id' => $receipt->user_id,
                    'is_posted' => $receipt->is_posted,
                    'comment' => $receipt->comment,
                    'overhead_costs' => $receipt->overhead_costs,
                    'project' => $receipt->project,
                    'created_at' => $receipt->created_at,
                    'updated_at' => $receipt->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $receipts->currentPage(),
                    'last_page' => $receipts->lastPage(),
                    'per_page' => $receipts->perPage(),
                    'total' => $receipts->total()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка оприходований: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить оприходование по ID
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            // Получаем основную информацию об оприходовании с JOIN'ами
            $receipt = DB::table('receipts as r')
                ->leftJoin('warehouses as w', 'r.warehouse', '=', 'w.id')
                ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
                ->select(
                    'r.id', 'r.number', 'r.date', 'r.organization',
                    'r.warehouse as warehouse_id',
                    'w.name as warehouse_name',
                    'w.address as warehouse_address',
                    'r.status', 'r.total', 'r.created_by', 'r.user_id', 'r.is_posted',
                    'r.comment', 'r.overhead_costs', 'r.project', 'r.created_at', 'r.updated_at',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as user_full_name")
                )
                ->where('r.user_id', $user->id)
                ->where('r.id', $id)
                ->first();

            if (!$receipt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Оприходование не найдено'
                ], 404);
            }

            // Получаем позиции
            $positions = DB::table('receipt_positions')
                ->where('receipt_id', $id)
                ->get();

            // Получаем файлы
            $files = DB::table('receipt_files')
                ->where('receipt_id', $id)
                ->get();

            // Формируем данные с информацией о пользователе и складе
            $data = [
                'id' => $receipt->id,
                'number' => $receipt->number,
                'date' => $receipt->date,
                'organization' => $receipt->organization,
                'project' => $receipt->project,
                'warehouse' => $receipt->warehouse_id,
                'warehouse_name' => $receipt->warehouse_name ?? '',
                'warehouse_address' => $receipt->warehouse_address ?? '',
                'status' => $receipt->status,
                'is_posted' => $receipt->is_posted,
                'comment' => $receipt->comment,
                'total' => $receipt->total,
                'overhead_costs' => $receipt->overhead_costs,
                'created_by' => $receipt->user_full_name ?? $receipt->created_by,
                'user_id' => $receipt->user_id,
                'created_at' => $receipt->created_at,
                'updated_at' => $receipt->updated_at,
                'positions' => $positions,
                'files' => $files
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении оприходования: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Создать новое оприходование
     */
    public function store(Request $request): JsonResponse
    {
        Log::info('RECEIPT FILES', ['receipt_files' => $request->receipt_files, 'all' => $request->all()]);
        try {
            $validator = Validator::make($request->all(), [
                'number' => 'required|string|max:50',
                'date' => 'required|date',
                'status' => 'nullable|string|in:draft,posted',
                'is_posted' => 'nullable|boolean',
                'organization' => 'required|string|max:255',
                'project' => 'nullable|string|max:255',
                'warehouse' => 'required|integer|exists:warehouses,id',
                'comment' => 'nullable|string',
                'overhead_costs' => 'nullable|numeric|min:0',
                'total' => 'nullable|numeric|min:0',
                'positions' => 'nullable|array',
                'positions.*.name' => 'nullable|string|max:255',
                'positions.*.code' => 'nullable|string|max:255',
                'positions.*.barcode' => 'nullable|string|max:255',
                'positions.*.article' => 'nullable|string|max:255',
                'positions.*.quantity' => 'nullable|numeric|min:0',
                'positions.*.balance' => 'nullable|numeric|min:0',
                'positions.*.price' => 'nullable|numeric|min:0',
                'positions.*.reason' => 'nullable|string|max:255',
                'positions.*.gtd' => 'nullable|string|max:255',
                'positions.*.rnpt' => 'nullable|string|max:255',
                'positions.*.country' => 'nullable|string|max:255',
                'receipt_files' => 'nullable|array',
                'receipt_files.*.filename' => 'nullable|string|max:255',
                'receipt_files.*.size_mb' => 'nullable|numeric|min:0',
                'receipt_files.*.file_url' => 'nullable|string|max:500',
                'receipt_files.*.employee' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $user = $request->user();
            $receipt = Receipt::create([
                'number' => $request->number,
                'date' => $request->date,
                'status' => $request->status ?? 'draft',
                'is_posted' => $request->is_posted ?? false,
                'organization' => $request->organization,
                'project' => $request->project,
                'warehouse' => $request->warehouse,
                'comment' => $request->comment,
                'overhead_costs' => $request->overhead_costs ?? 0,
                'total' => $request->total ?? 0,
                'user_id' => $user ? $user->id : null,
                'created_by' => $user ? $user->name : null
            ]);

            // Создаем позиции
            $totalAmount = 0;
            if ($request->has('positions') && is_array($request->positions)) {
                foreach ($request->positions as $positionData) {
                    $quantity = $positionData['quantity'] ?? 0;
                    $price = $positionData['price'] ?? 0;
                    $amount = $quantity * $price;
                    $totalAmount += $amount;
                    
                    // Находим товар по артикулу или названию
                    $product = null;
                    $article = $positionData['article'] ?? '';
                    $name = $positionData['name'] ?? '';
                    
                    if (!empty($article)) {
                        $product = \App\Models\ProductSklad::where('article', $article)->first();
                        Log::info('Поиск товара по артикулу', [
                            'article' => $article,
                            'found' => $product ? $product->id : null
                        ]);
                    }
                    
                    if (!$product && !empty($name)) {
                        $product = \App\Models\ProductSklad::where('name', $name)->first();
                        Log::info('Поиск товара по названию', [
                            'name' => $name,
                            'found' => $product ? $product->id : null
                        ]);
                    }
                    
                    Log::info('Создание позиции', [
                        'position_data' => $positionData,
                        'product_id' => $product ? $product->id : null,
                        'article' => $article,
                        'name' => $name
                    ]);
                    
                    ReceiptPosition::create([
                        'receipt_id' => $receipt->id,
                        'product_id' => $product ? $product->id : null,
                        'name' => $positionData['name'] ?? '',
                        'code' => $positionData['code'] ?? '',
                        'barcode' => $positionData['barcode'] ?? '',
                        'article' => $positionData['article'] ?? '',
                        'quantity' => $quantity,
                        'balance' => $positionData['balance'] ?? 0,
                        'price' => $price,
                        'amount' => $amount,
                        'reason' => $positionData['reason'] ?? '',
                        'gtd' => $positionData['gtd'] ?? '',
                        'rnpt' => $positionData['rnpt'] ?? '',
                        'country' => $positionData['country'] ?? ''
                    ]);
                }
            }

            // Обновляем общую сумму оприходования
            $receipt->update([
                'total' => $totalAmount + ($request->overhead_costs ?? 0)
            ]);

            // Создаем записи файлов
            if ($request->has('receipt_files') && is_array($request->receipt_files)) {
                foreach ($request->receipt_files as $fileData) {
                    DB::table('receipt_files')->insert([
                        'receipt_id' => $receipt->id,
                        'filename' => $fileData['filename'] ?? '',
                        'size_mb' => $fileData['size_mb'] ?? 0,
                        'file_url' => $fileData['file_url'] ?? '',
                        'employee' => $fileData['employee'] ?? '',
                        'uploaded_at' => now()
                    ]);
                }
            }

            // Если оприходование проведено, обновляем остатки
            if ($receipt->is_posted || $receipt->status === 'posted') {
                $this->updateProductBalances($receipt);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Оприходование успешно создано',
                'data' => $receipt->load('positions')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании оприходования: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить оприходование
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $receipt = Receipt::where('user_id', $user->id)->find($id);

            if (!$receipt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Оприходование не найдено'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'number' => 'nullable|string|max:255',
                'date' => 'nullable|date',
                'status' => 'nullable|string|in:draft,posted',
                'is_posted' => 'nullable|boolean',
                'organization' => 'nullable|string|max:255',
                'project' => 'nullable|string|max:255',
                'warehouse' => 'nullable|integer|exists:warehouses,id',
                'comment' => 'nullable|string',
                'overhead_costs' => 'nullable|numeric|min:0',
                'total' => 'nullable|numeric|min:0',
                'files' => 'nullable|array',
                'tasks' => 'nullable|array',
                'positions' => 'nullable|array',
                'positions.*.name' => 'nullable|string|max:255',
                'positions.*.code' => 'nullable|string|max:255',
                'positions.*.barcode' => 'nullable|string|max:255',
                'positions.*.article' => 'nullable|string|max:255',
                'positions.*.quantity' => 'nullable|numeric|min:0',
                'positions.*.balance' => 'nullable|numeric|min:0',
                'positions.*.price' => 'nullable|numeric|min:0',
                'positions.*.reason' => 'nullable|string|max:255',
                'positions.*.gtd' => 'nullable|string|max:255',
                'positions.*.rnpt' => 'nullable|string|max:255',
                'positions.*.country' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $receipt->update([
                'number' => $request->number,
                'date' => $request->date,
                'status' => $request->status,
                'is_posted' => $request->is_posted,
                'organization' => $request->organization,
                'project' => $request->project,
                'warehouse' => $request->warehouse,
                'comment' => $request->comment,
                'overhead_costs' => $request->overhead_costs,
                'total' => $request->total
            ]);

            // Обновляем позиции
            if ($request->has('positions')) {
                // Удаляем старые позиции
                $receipt->positions()->delete();

                // Создаем новые позиции
                $totalAmount = 0;
                if (is_array($request->positions)) {
                    foreach ($request->positions as $positionData) {
                        $quantity = $positionData['quantity'] ?? 0;
                        $price = $positionData['price'] ?? 0;
                        $amount = $quantity * $price;
                        $totalAmount += $amount;
                        
                        // Находим товар по артикулу или названию
                        $product = \App\Models\ProductSklad::where('article', $positionData['article'] ?? '')
                            ->orWhere('name', $positionData['name'] ?? '')
                            ->first();
                        
                        ReceiptPosition::create([
                            'receipt_id' => $receipt->id,
                            'product_id' => $product ? $product->id : null,
                            'name' => $positionData['name'] ?? '',
                            'code' => $positionData['code'] ?? '',
                            'barcode' => $positionData['barcode'] ?? '',
                            'article' => $positionData['article'] ?? '',
                            'quantity' => $quantity,
                            'balance' => $positionData['balance'] ?? 0,
                            'price' => $price,
                            'amount' => $amount,
                            'reason' => $positionData['reason'] ?? '',
                            'gtd' => $positionData['gtd'] ?? '',
                            'rnpt' => $positionData['rnpt'] ?? '',
                            'country' => $positionData['country'] ?? ''
                        ]);
                    }
                }
                
                // Обновляем общую сумму оприходования
                $receipt->update([
                    'total' => $totalAmount + ($request->overhead_costs ?? 0)
                ]);
            }

            // Если статус изменился на "проведено", обновляем остатки
            if (($request->status === 'posted' || $request->is_posted) && 
                ($receipt->status !== 'posted' || !$receipt->is_posted)) {
                $this->updateProductBalances($receipt);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Оприходование успешно обновлено',
                'data' => $receipt->load('positions')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении оприходования: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить оприходование
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $receipt = Receipt::where('user_id', $user->id)->find($id);

            if (!$receipt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Оприходование не найдено'
                ], 404);
            }

            $receipt->delete();

            return response()->json([
                'success' => true,
                'message' => 'Оприходование успешно удалено'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении оприходования: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить остатки товаров при проведении оприходования
     */
    private function updateProductBalances(Receipt $receipt)
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