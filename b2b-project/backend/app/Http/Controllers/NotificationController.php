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
            // Получаем пользователя
            $user = Auth::user();
            
            // Если пользователь не аутентифицирован, используем тестового пользователя
            if (!$user) {
                $user = \App\Models\User::find(52);
                Log::info('Using fallback user for notifications', ['user_id' => $user ? $user->id : 'not found']);
            }
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не найден'
                ], 404);
            }
            
            $limit = $request->get('limit', 50);
            $type = $request->get('type');
            $isRead = $request->get('is_read');

            Log::info('Notifications filter request', [
                'user_id' => $user->id,
                'auth_user_id' => Auth::id(),
                'type' => $type,
                'is_read' => $isRead,
                'limit' => $limit,
                'request_params' => $request->all()
            ]);

            $query = Notification::where('user_id', $user->id);

            // Фильтр по типу
            if ($type && $type !== '') {
                Log::info('Applying type filter', ['type' => $type]);
                $query->where('type', $type);
            }

            // Фильтр по статусу прочтения
            if ($isRead !== null && $isRead !== '') {
                // Преобразуем строку в boolean
                $isReadBoolean = null;
                
                if ($isRead === 'true' || $isRead === '1') {
                    $isReadBoolean = true;
                } elseif ($isRead === 'false' || $isRead === '0') {
                    $isReadBoolean = false;
                }
                
                if ($isReadBoolean !== null) {
                    Log::info('Applying read status filter', [
                        'is_read_param' => $isRead,
                        'is_read_boolean' => $isReadBoolean
                    ]);
                    $query->where('is_read', $isReadBoolean);
                }
            }

            $notifications = $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            Log::info('Notifications query result', [
                'count' => $notifications->count(),
                'filters_applied' => [
                    'type' => $type,
                    'is_read' => $isRead
                ],
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'unread_count' => Notification::getUnreadCount($user->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при получении уведомлений: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
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
            $user = Auth::user() ?: \App\Models\User::find(52);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не найден'
                ], 404);
            }
            
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
            $user = Auth::user() ?: \App\Models\User::find(52);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не найден'
                ], 404);
            }
            
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
            $user = Auth::user() ?: \App\Models\User::find(52);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не найден'
                ], 404);
            }
            
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
            $user = Auth::user() ?: \App\Models\User::find(52);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не найден'
                ], 404);
            }
            
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
            $user = Auth::user() ?: \App\Models\User::find(52);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не найден'
                ], 404);
            }
            
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