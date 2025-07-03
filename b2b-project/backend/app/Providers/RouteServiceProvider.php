<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Rate limiting для API - более мягкие ограничения
        RateLimiter::for('api', function (Request $request) {
            // Увеличиваем лимит для лучшей поддержки международных пользователей
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        // Специальный rate limiting для аутентификации
        RateLimiter::for('auth', function (Request $request) {
            // Более мягкие ограничения для регистрации и входа
            return Limit::perMinute(30)->by($request->ip())->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please try again later.'
                ], 429);
            });
        });

        // Rate limiting для регистрации
        RateLimiter::for('register', function (Request $request) {
            // Ограничение: 10 попыток регистрации в час с одного IP
            return Limit::perHour(10)->by($request->ip())->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many registration attempts. Please try again in an hour.'
                ], 429);
            });
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
} 