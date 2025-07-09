<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable(); // Номер оприходования
            $table->datetime('date')->nullable(); // Дата оприходования
            $table->string('status')->default('draft'); // Статус: draft, posted
            $table->boolean('is_posted')->default(false); // Проведено
            $table->string('organization')->nullable(); // Организация
            $table->string('project')->nullable(); // Проект
            $table->string('warehouse')->nullable(); // Склад
            $table->text('comment')->nullable(); // Комментарий
            $table->decimal('overhead_costs', 15, 2)->default(0); // Накладные расходы
            $table->decimal('total', 15, 2)->default(0); // Итого
            $table->json('files')->nullable(); // Файлы (JSON массив)
            $table->json('tasks')->nullable(); // Задачи (JSON массив)
            $table->timestamps();
        });

        Schema::create('receipt_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained('receipts')->onDelete('cascade');
            $table->string('name')->nullable(); // Наименование
            $table->string('code')->nullable(); // Код
            $table->string('barcode')->nullable(); // Штрихкод
            $table->string('article')->nullable(); // Артикул
            $table->decimal('quantity', 15, 3)->default(0); // Количество
            $table->decimal('balance', 15, 3)->default(0); // Остаток
            $table->decimal('price', 15, 2)->default(0); // Цена
            $table->string('reason')->nullable(); // Причина
            $table->string('gtd')->nullable(); // ГТД
            $table->string('rnpt')->nullable(); // РНПТ
            $table->string('country')->nullable(); // Страна
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_positions');
        Schema::dropIfExists('receipts');
    }
}; 