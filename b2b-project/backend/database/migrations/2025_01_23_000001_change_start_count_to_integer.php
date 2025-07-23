<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Изменяем тип поля start_count с NUMERIC на INTEGER
        DB::statement('ALTER TABLE products_sklad ALTER COLUMN start_count TYPE INTEGER USING start_count::INTEGER');
        
        // Обновляем комментарий
        DB::statement("COMMENT ON COLUMN products_sklad.start_count IS 'Начальный остаток товара (целое число)'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем обратно к NUMERIC(15,3)
        DB::statement('ALTER TABLE products_sklad ALTER COLUMN start_count TYPE NUMERIC(15,3) USING start_count::NUMERIC(15,3)');
        
        // Возвращаем старый комментарий
        DB::statement("COMMENT ON COLUMN products_sklad.start_count IS 'Начальный остаток товара'");
    }
}; 