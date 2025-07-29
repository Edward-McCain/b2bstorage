<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Определяем, используем ли мы локальную или серверную базу данных
        $useLocalDb = env('LOCAL_DB', true);

        if ($useLocalDb) {
            // Используем локальную базу данных
            $this->configureLocalDatabase();
        } else {
            // Используем серверную базу данных
            $this->configureServerDatabase();
        }
    }

    /**
     * Настройка локальной базы данных
     */
    private function configureLocalDatabase(): void
    {
        Config::set('database.connections.pgsql.host', env('DB_HOST', '127.0.0.1'));
        Config::set('database.connections.pgsql.port', env('DB_PORT', '5432'));
        Config::set('database.connections.pgsql.database', env('DB_DATABASE', 'b2bs_local'));
        Config::set('database.connections.pgsql.username', env('DB_USERNAME', 'b2bs_user'));
        Config::set('database.connections.pgsql.password', env('DB_PASSWORD', 'b2bs_password'));
    }

    /**
     * Настройка серверной базы данных
     */
    private function configureServerDatabase(): void
    {
        Config::set('database.connections.pgsql.host', env('SERVER_DB_HOST', '5.35.85.110'));
        Config::set('database.connections.pgsql.port', env('SERVER_DB_PORT', '5432'));
        Config::set('database.connections.pgsql.database', env('SERVER_DB_DATABASE', 'b2bstorage'));
        Config::set('database.connections.pgsql.username', env('SERVER_DB_USERNAME', 'b2buser'));
        Config::set('database.connections.pgsql.password', env('SERVER_DB_PASSWORD', 'B2B_Storage_2024!'));
    }
} 