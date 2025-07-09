<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\ReceiptFile;

class ReceiptFileController extends Controller
{
    /**
     * Загрузить файл для оприходования
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:8192',
            'receipt_id' => 'required|integer|exists:receipts,id'
        ]);

        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        // Проверяем, что оприходование принадлежит пользователю
        $receipt = DB::table('receipts')->where('id', $request->receipt_id)->where('user_id', $user->id)->first();
        
        if (!$receipt) {
            return response()->json([
                'success' => false,
                'message' => 'Оприходование не найдено или доступ запрещен'
            ], 404);
        }

        $file = $request->file('file');
        $filename = uniqid('receipt_', true) . '.' . $file->getClientOriginalExtension();
        $path = 'uploads/receipts/' . $filename;

        // Сохраняем файл
        Storage::disk('public')->put($path, file_get_contents($file->getPathname()));

        // Получаем полный URL файла
        $fileUrl = request()->getSchemeAndHttpHost() . '/storage/' . $path;

        // Сохраняем в БД
        $receiptFile = ReceiptFile::create([
            'receipt_id' => $request->receipt_id,
            'filename' => $file->getClientOriginalName(),
            'size_mb' => round($file->getSize() / 1048576, 2),
            'file_url' => $fileUrl,
            'employee' => $user ? $user->first_name . ' ' . $user->last_name : '—',
            'uploaded_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Файл успешно загружен',
            'data' => $receiptFile
        ], 201);
    }

    /**
     * Получить файлы для оприходования
     */
    public function getFiles(Request $request, $receiptId)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        // Проверяем, что оприходование принадлежит пользователю
        $receipt = DB::table('receipts')->where('id', $receiptId)->where('user_id', $user->id)->first();
        
        if (!$receipt) {
            return response()->json([
                'success' => false,
                'message' => 'Оприходование не найдено'
            ], 404);
        }

        $files = ReceiptFile::where('receipt_id', $receiptId)
            ->orderBy('uploaded_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $files
        ]);
    }

    /**
     * Удалить файл
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        $file = ReceiptFile::find($id);
        
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'Файл не найден'
            ], 404);
        }

        // Проверяем, что файл принадлежит оприходованию пользователя
        $receipt = DB::table('receipts')->where('id', $file->receipt_id)->where('user_id', $user->id)->first();
        
        if (!$receipt) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }

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
    }

    /**
     * Загрузить файл в черновик (для совместимости)
     */
    public function storeDraft(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:8192',
        ]);

        $user = $request->user();
        $file = $request->file('file');
        $filename = uniqid('receipt_', true) . '.' . $file->getClientOriginalExtension();
        $path = 'uploads/receipts/' . $filename;

        Storage::disk('public')->put($path, file_get_contents($file->getPathname()));

        // Получаем полный URL файла
        $fileUrl = request()->getSchemeAndHttpHost() . '/storage/' . $path;

        // Сохраняем имя пользователя в employee
        $employee = $user ? $user->first_name . ' ' . $user->last_name : '—';

        return response()->json([
            'id' => $filename,
            'filename' => $file->getClientOriginalName(),
            'size_mb' => round($file->getSize() / 1048576, 2),
            'file_url' => $fileUrl,
            'uploaded_at' => now()->toDateTimeString(),
            'employee' => $employee,
        ]);
    }
} 