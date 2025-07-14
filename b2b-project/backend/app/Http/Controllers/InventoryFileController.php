<?php

namespace App\Http\Controllers;

use App\Models\InventoryFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class InventoryFileController extends Controller
{
    /**
     * Загрузить файл как черновик (без привязки к инвентаризации)
     */
    public function uploadDraft(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:10240', // Максимум 10MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid() . '_' . time() . '.' . $extension;
            
            // Сохраняем файл
            $path = $file->storeAs('inventory-files', $filename, 'public');
            
            // Получаем полный URL файла
            $fileUrl = request()->getSchemeAndHttpHost() . '/storage/' . $path;
            
            Log::info('Файл инвентаризации загружен как черновик', [
                'filename' => $filename,
                'original_name' => $originalName,
                'file_url' => $fileUrl,
                'size' => $file->getSize()
            ]);

            return response()->json([
                'id' => $filename,
                'filename' => $originalName,
                'file_url' => $fileUrl,
                'file_size' => $file->getSize(),
                'size_mb' => round($file->getSize() / 1048576, 2),
                'uploaded_by' => Auth::user()->first_name ?? 'Система',
                'uploaded_at' => now()->toDateTimeString()
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке файла: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Загрузить файл с привязкой к инвентаризации
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:10240', // Максимум 10MB
                'inventory_id' => 'required|exists:inventories,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid() . '_' . time() . '.' . $extension;
            
            // Сохраняем файл
            $path = $file->storeAs('inventory-files', $filename, 'public');
            
            // Создаем запись в базе данных
            $inventoryFile = InventoryFile::create([
                'filename' => $filename,
                'original_filename' => $originalName,
                'file_path' => Storage::url($path),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => Auth::id(),
                'inventory_id' => $request->inventory_id
            ]);
            
            Log::info('Файл инвентаризации создан', [
                'file_id' => $inventoryFile->id,
                'inventory_id' => $request->inventory_id,
                'filename' => $filename,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Файл загружен успешно',
                'data' => [
                    'id' => $inventoryFile->id,
                    'filename' => $filename,
                    'original_filename' => $originalName,
                    'file_url' => Storage::url($path),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => Auth::user()->first_name ?? 'Система'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке файла: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить файл
     */
    public function destroy($id): JsonResponse
    {
        try {
            $file = InventoryFile::find($id);

            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'Файл не найден'
                ], 404);
            }

            // Удаляем физический файл
            $filePath = str_replace('/storage/', 'public/', $file->file_path);
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            // Удаляем запись из базы данных
            $file->delete();

            return response()->json([
                'success' => true,
                'message' => 'Файл удален успешно'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении файла: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить файл
     */
    public function show($id): JsonResponse
    {
        try {
            $file = InventoryFile::with(['inventory', 'uploadedBy'])->find($id);

            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'Файл не найден'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $file
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении файла: ' . $e->getMessage()
            ], 500);
        }
    }
} 