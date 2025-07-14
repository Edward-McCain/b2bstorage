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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('calculated_quantity', 15, 3)->default(0);
            $table->decimal('actual_quantity', 15, 3)->default(0);
            $table->decimal('difference_quantity', 15, 3)->storedAs('actual_quantity - calculated_quantity');
            $table->enum('excess_shortage', ['normal', 'excess', 'shortage'])->storedAs(
                "CASE 
                    WHEN (actual_quantity - calculated_quantity) > 0 THEN 'excess'
                    WHEN (actual_quantity - calculated_quantity) < 0 THEN 'shortage'
                    ELSE 'normal'
                END"
            );
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
}; 