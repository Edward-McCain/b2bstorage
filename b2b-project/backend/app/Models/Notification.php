<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Типы уведомлений
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_RECOMMENDATION = 'recommendation';
    const TYPE_LOW_STOCK = 'low_stock';
    const TYPE_OVERDUE = 'overdue';

    /**
     * Отношение к пользователю
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить непрочитанные уведомления для пользователя
     */
    public static function getUnreadForUser($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Получить все уведомления для пользователя
     */
    public static function getAllForUser($userId, $limit = 50)
    {
        return self::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Отметить уведомление как прочитанное
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Отметить все уведомления пользователя как прочитанные
     */
    public static function markAllAsRead($userId)
    {
        self::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Получить количество непрочитанных уведомлений
     */
    public static function getUnreadCount($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
} 