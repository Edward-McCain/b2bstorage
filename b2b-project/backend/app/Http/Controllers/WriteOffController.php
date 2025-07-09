<?php

namespace App\Http\Controllers;

use App\Models\WriteOff;
use App\Models\WriteOffPosition;
use App\Models\WriteOffFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WriteOffController extends Controller
{
    /**
     * Получить все списания пользователя
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $query = DB::table('write_offs')
            ->leftJoin('warehouses', 'write_offs.warehouse', '=', 'warehouses.id')
            ->leftJoin('users', 'write_offs.user_id', '=', 'users.id')
            ->where('write_offs.user_id', $user->id)
            ->select([
                'write_offs.*',
                'warehouses.name as warehouse_name',
                'warehouses.address as warehouse_address',
                'users.first_name as created_by'
            ]);

        // Фильтры
        if ($request->filled('number')) {
            $query->where('write_offs.number', 'like', '%' . $request->number . '%');
        }

        if ($request->filled('date_from')) {
            $query->where('write_offs.date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('write_offs.date', '<=', $request->date_to);
        }

        if ($request->filled('warehouse')) {
            $query->where('write_offs.warehouse', $request->warehouse);
        }

        if ($request->filled('status')) {
            $query->where('write_offs.status', $request->status);
        }

        $writeOffs = $query->orderBy('write_offs.created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $writeOffs
        ]);
    }

    /**
     * Получить одно списание
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $writeOff = DB::table('write_offs')
            ->leftJoin('warehouses', 'write_offs.warehouse', '=', 'warehouses.id')
            ->leftJoin('users', 'write_offs.user_id', '=', 'users.id')
            ->where('write_offs.id', $id)
            ->where('write_offs.user_id', $user->id)
            ->select([
                'write_offs.id', 'write_offs.number', 'write_offs.date', 'write_offs.organization',
                'write_offs.warehouse as warehouse_id',
                'warehouses.name as warehouse_name',
                'warehouses.address as warehouse_address',
                'write_offs.status', 'write_offs.total', 'write_offs.created_by', 'write_offs.user_id',
                'write_offs.comment', 'write_offs.overhead_costs', 'write_offs.project', 
                'write_offs.created_at', 'write_offs.updated_at',
                'users.first_name as created_by'
            ])
            ->first();

        if (!$writeOff) {
            return response()->json(['error' => 'Списание не найдено'], 404);
        }

        // Получаем позиции
        $positions = DB::table('write_off_positions')
            ->where('write_off_id', $id)
            ->get();

        // Получаем файлы
        $files = DB::table('write_off_files')
            ->where('write_off_id', $id)
            ->get();

        $writeOff->positions = $positions;
        $writeOff->files = $files;

        return response()->json([
            'success' => true,
            'data' => $writeOff
        ]);
    }

    /**
     * Создать новое списание
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }



        $request->validate([
            'number' => 'required|string|max:50',
            'date' => 'required|date',
            'organization' => 'required|string|max:255',
            'project' => 'nullable|string|max:255',
            'warehouse' => 'required|integer',
            'status' => 'nullable|string|max:50',
            'comment' => 'nullable|string',
            'overhead_costs' => 'nullable|numeric',
            'positions' => 'nullable|array',
            'positions.*.name' => 'required|string',
            'positions.*.quantity' => 'required|numeric',
            'positions.*.price' => 'required|numeric',
            'write_off_files' => 'nullable|array',
            'write_off_files.*.filename' => 'nullable|string|max:255',
            'write_off_files.*.size_mb' => 'nullable|numeric|min:0',
            'write_off_files.*.file_url' => 'nullable|string|max:500',
            'write_off_files.*.employee' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();

        try {
            // Создаем списание
            $writeOff = WriteOff::create([
                'number' => $request->number,
                'date' => $request->date,
                'organization' => $request->organization,
                'project' => $request->project,
                'warehouse' => $request->warehouse,
                'status' => $request->status ?? 'draft',
                'comment' => $request->comment,
                'overhead_costs' => $request->overhead_costs ?? 0,
                'created_by' => $user->first_name,
                'user_id' => $user->id
            ]);

            // Добавляем позиции
            if ($request->has('positions')) {
                foreach ($request->positions as $positionData) {
                    $amount = $positionData['quantity'] * $positionData['price'];
                    
                    WriteOffPosition::create([
                        'write_off_id' => $writeOff->id,
                        'name' => $positionData['name'],
                        'code' => $positionData['code'] ?? '',
                        'article' => $positionData['article'] ?? '',
                        'quantity' => $positionData['quantity'],
                        'price' => $positionData['price'],
                        'amount' => $amount,
                        'reason' => $positionData['reason'] ?? '',
                        'gtd' => $positionData['gtd'] ?? '',
                        'rnpt' => $positionData['rnpt'] ?? '',
                        'country' => $positionData['country'] ?? '',
                        'product_id' => $positionData['product_id'] ?? null
                    ]);
                }
            }

            // Создаем записи файлов
            if ($request->has('write_off_files') && is_array($request->write_off_files)) {
                foreach ($request->write_off_files as $fileData) {
                    DB::table('write_off_files')->insert([
                        'write_off_id' => $writeOff->id,
                        'filename' => $fileData['filename'] ?? '',
                        'size_mb' => $fileData['size_mb'] ?? 0,
                        'file_url' => $fileData['file_url'] ?? '',
                        'employee' => $fileData['employee'] ?? '',
                        'uploaded_at' => now()
                    ]);
                }
            }

            // Рассчитываем общую сумму
            $total = WriteOffPosition::where('write_off_id', $writeOff->id)->sum('amount');
            $total += $request->overhead_costs ?? 0;
            
            $writeOff->update(['total' => $total]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Списание успешно создано',
                'data' => $writeOff
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании списания: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить списание
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $writeOff = WriteOff::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$writeOff) {
            return response()->json(['error' => 'Списание не найдено'], 404);
        }

        $request->validate([
            'number' => 'required|string|max:50',
            'date' => 'required|date',
            'organization' => 'required|string|max:255',
            'project' => 'nullable|string|max:255',
            'warehouse' => 'required|integer',
            'status' => 'nullable|string|max:50',
            'comment' => 'nullable|string',
            'overhead_costs' => 'nullable|numeric'
        ]);

        DB::beginTransaction();

        try {
            // Обновляем списание
            $writeOff->update([
                'number' => $request->number,
                'date' => $request->date,
                'organization' => $request->organization,
                'project' => $request->project,
                'warehouse' => $request->warehouse,
                'status' => $request->status ?? 'draft',
                'comment' => $request->comment,
                'overhead_costs' => $request->overhead_costs ?? 0
            ]);

            // Обновляем позиции если переданы
            if ($request->has('positions')) {
                // Удаляем старые позиции
                WriteOffPosition::where('write_off_id', $id)->delete();
                
                // Добавляем новые позиции
                foreach ($request->positions as $positionData) {
                    $amount = $positionData['quantity'] * $positionData['price'];
                    
                    WriteOffPosition::create([
                        'write_off_id' => $writeOff->id,
                        'name' => $positionData['name'],
                        'code' => $positionData['code'] ?? '',
                        'article' => $positionData['article'] ?? '',
                        'quantity' => $positionData['quantity'],
                        'price' => $positionData['price'],
                        'amount' => $amount,
                        'reason' => $positionData['reason'] ?? '',
                        'gtd' => $positionData['gtd'] ?? '',
                        'rnpt' => $positionData['rnpt'] ?? '',
                        'country' => $positionData['country'] ?? '',
                        'product_id' => $positionData['product_id'] ?? null
                    ]);
                }
            }

            // Обновляем файлы если переданы
            if ($request->has('write_off_files')) {
                // Удаляем старые файлы
                WriteOffFile::where('write_off_id', $writeOff->id)->delete();
                
                // Создаем новые записи файлов
                if (is_array($request->write_off_files)) {
                    foreach ($request->write_off_files as $fileData) {
                        DB::table('write_off_files')->insert([
                            'write_off_id' => $writeOff->id,
                            'filename' => $fileData['filename'] ?? '',
                            'size_mb' => $fileData['size_mb'] ?? 0,
                            'file_url' => $fileData['file_url'] ?? '',
                            'employee' => $fileData['employee'] ?? '',
                            'uploaded_at' => now()
                        ]);
                    }
                }
            }

            // Рассчитываем общую сумму
            $total = WriteOffPosition::where('write_off_id', $writeOff->id)->sum('amount');
            $total += $request->overhead_costs ?? 0;
            
            $writeOff->update(['total' => $total]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Списание успешно обновлено',
                'data' => $writeOff
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении списания: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить списание
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $writeOff = WriteOff::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$writeOff) {
            return response()->json(['error' => 'Списание не найдено'], 404);
        }

        $writeOff->delete();

        return response()->json([
            'success' => true,
            'message' => 'Списание успешно удалено'
        ]);
    }
} 