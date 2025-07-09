<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReceiptFileController extends Controller
{
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

        // Сохраняем имя пользователя в employee
        $employee = $user ? $user->name : '—';

        // Здесь можно добавить сохранение в БД, если нужно

        return response()->json([
            'id' => $filename,
            'filename' => $file->getClientOriginalName(),
            'size_mb' => round($file->getSize() / 1048576, 2),
            'uploaded_at' => now()->toDateTimeString(),
            'employee' => $employee,
        ]);
    }
} 