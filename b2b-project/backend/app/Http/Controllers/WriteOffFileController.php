<?php

namespace App\Http\Controllers;

use App\Models\WriteOffFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class WriteOffFileController extends Controller
{
    /**
     * Загрузить файл для списания
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'write_off_id' => 'required|integer'
        ]);

        try {
            $file = $request->file('file');
            $filename = uniqid('write_off_', true) . '.' . $file->getClientOriginalExtension();
            $path = 'uploads/write_offs/' . $filename;
            $size_mb = round($file->getSize() / 1048576, 2);
            
            // Сохраняем файл
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            
            // Получаем полный URL файла
            $fileUrl = request()->getSchemeAndHttpHost() . '/storage/' . $path;

            // Создаем запись в БД
            $writeOffFile = WriteOffFile::create([
                'write_off_id' => $request->write_off_id,
                'filename' => $file->getClientOriginalName(),
                'size_mb' => $size_mb,
                'employee' => $user->first_name,
                'file_url' => $fileUrl
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Файл успешно загружен',
                'data' => $writeOffFile
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке файла: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить файлы списания
     */
    public function getFiles($writeOffId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $files = WriteOffFile::where('write_off_id', $writeOffId)->get();

        return response()->json([
            'success' => true,
            'data' => $files
        ]);
    }

    /**
     * Удалить файл
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $file = WriteOffFile::find($id);

        if (!$file) {
            return response()->json(['error' => 'Файл не найден'], 404);
        }

        try {
            // Удаляем физический файл
            if ($file->file_url) {
                $path = str_replace('/storage/', '', $file->file_url);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            // Удаляем запись из БД
            $file->delete();

            return response()->json([
                'success' => true,
                'message' => 'Файл успешно удален'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении файла: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Сохранить файл как черновик
     */
    public function storeDraft(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $request->validate([
            'file' => 'required|file|max:10240',
            'write_off_id' => 'nullable|integer'
        ]);

        try {
            $file = $request->file('file');
            $filename = uniqid('write_off_', true) . '.' . $file->getClientOriginalExtension();
            $path = 'uploads/write_offs/' . $filename;
            $size_mb = round($file->getSize() / 1048576, 2);
            
            // Сохраняем файл
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            
            // Получаем полный URL файла
            $fileUrl = request()->getSchemeAndHttpHost() . '/storage/' . $path;

            return response()->json([
                'id' => $filename,
                'filename' => $file->getClientOriginalName(),
                'size_mb' => round($file->getSize() / 1048576, 2),
                'file_url' => $fileUrl,
                'uploaded_at' => now()->toDateTimeString(),
                'employee' => $user->first_name,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке файла: ' . $e->getMessage()
            ], 500);
        }
    }
} 