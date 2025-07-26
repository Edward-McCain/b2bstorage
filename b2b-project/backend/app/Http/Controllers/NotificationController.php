<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Получить все уведомления пользователя
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $limit = $request->get('limit', 50);
            $type = $request->get('type');
            $isRead = $request->get('is_read');

            $query = Notification::where('user_id', $user->id);

            // Фильтр по типу
            if ($type) {
                $query->where('type', $type);
            }

            // Фильтр по статусу прочтения
            if ($isRead !== null) {
                $query->where('is_read', $isRead);
            }

            $notifications = $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'unread_count' => Notification::getUnreadCount($user->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при получении уведомлений: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении уведомлений'
            ], 500);
        }
    }

    /**
     * Получить непрочитанные уведомления
     */
    public function unread(): JsonResponse
    {
        try {
            $user = Auth::user();
            $notifications = Notification::getUnreadForUser($user->id);

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'count' => count($notifications)
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при получении непрочитанных уведомлений: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении непрочитанных уведомлений'
            ], 500);
        }
    }

    /**
     * Отметить уведомление как прочитанное
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $notification = Notification::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Уведомление не найдено'
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Уведомление отмечено как прочитанное'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при отметке уведомления как прочитанного: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отметке уведомления'
            ], 500);
        }
    }

    /**
     * Отметить все уведомления как прочитанные
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $user = Auth::user();
            Notification::markAllAsRead($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Все уведомления отмечены как прочитанные'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при отметке всех уведомлений как прочитанных: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отметке уведомлений'
            ], 500);
        }
    }

    /**
     * Удалить уведомление
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = Auth::user();
            $notification = Notification::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Уведомление не найдено'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Уведомление удалено'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при удалении уведомления: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении уведомления'
            ], 500);
        }
    }

    /**
     * Получить количество непрочитанных уведомлений
     */
    public function unreadCount(): JsonResponse
    {
        try {
            $user = Auth::user();
            $count = Notification::getUnreadCount($user->id);

            return response()->json([
                'success' => true,
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при получении количества непрочитанных уведомлений: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении количества уведомлений'
            ], 500);
        }
    }
} 