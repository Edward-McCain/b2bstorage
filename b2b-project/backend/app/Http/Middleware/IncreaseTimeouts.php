<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IncreaseTimeouts
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Увеличиваем таймауты для медленных операций
        set_time_limit(300); // 5 минут
        ini_set('max_execution_time', 300);
        ini_set('default_socket_timeout', 300);
        
        // Увеличиваем лимит памяти
        ini_set('memory_limit', '256M');
        
        return $next($request);
    }
} 