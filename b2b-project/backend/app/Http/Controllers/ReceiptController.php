<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\ReceiptPosition;
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
            $receipts = DB::table('receipts as r')
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
                ->orderBy('r.created_at', 'desc')
                ->paginate(20);

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
    public function show($id): JsonResponse
    {
        try {
            $receipt = Receipt::with('positions')->find($id);

            if (!$receipt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Оприходование не найдено'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $receipt
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
                    
                    ReceiptPosition::create([
                        'receipt_id' => $receipt->id,
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
                        'employee' => $fileData['employee'] ?? '',
                        'uploaded_at' => now()
                    ]);
                }
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
            $receipt = Receipt::find($id);

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
                        
                        ReceiptPosition::create([
                            'receipt_id' => $receipt->id,
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
    public function destroy($id): JsonResponse
    {
        try {
            $receipt = Receipt::find($id);

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
} 