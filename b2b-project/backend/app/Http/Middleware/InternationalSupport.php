<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternationalSupport
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Добавляем заголовки для лучшей поддержки международных пользователей
        $response = $next($request);

        // Добавляем заголовки для поддержки различных часовых поясов
        $response->headers->set('X-Timezone-Support', 'true');
        
        // Добавляем заголовок для поддержки различных локалей
        $response->headers->set('X-Locale-Support', 'true');
        
        // Добавляем заголовок для поддержки различных кодировок
        $response->headers->set('X-Encoding-Support', 'UTF-8');
        
        // Увеличиваем таймаут для медленных соединений
        if ($request->is('api/*')) {
            $response->headers->set('X-Request-Timeout', '300');
        }

        // Логирование запросов из Узбекистана для диагностики
        $clientIP = $request->ip();
        $userAgent = $request->userAgent();
        
        // Простая проверка на Узбекистан по IP (можно улучшить с помощью GeoIP)
        if ($this->isLikelyUzbekistan($clientIP, $userAgent)) {
            \Illuminate\Support\Facades\Log::info('Request from Uzbekistan', [
                'ip' => $clientIP,
                'user_agent' => $userAgent,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'timestamp' => now()->toISOString()
            ]);
        }

        return $response;
    }

    /**
     * Простая проверка на Узбекистан (можно улучшить с помощью GeoIP базы данных)
     */
    private function isLikelyUzbekistan(string $ip, string $userAgent): bool
    {
        // Проверка по User-Agent на узбекские домены или языки
        $uzbekIndicators = [
            '.uz',
            'uzbek',
            'uzbekistan',
            'uz_',
            'uz-'
        ];

        foreach ($uzbekIndicators as $indicator) {
            if (stripos($userAgent, $indicator) !== false) {
                return true;
            }
        }

        // Можно добавить проверку по IP диапазонам Узбекистана
        // Это упрощенная версия, для продакшена лучше использовать GeoIP
        return false;
    }
} 