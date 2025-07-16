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
        Schema::table('products_sklad', function (Blueprint $table) {
            $table->integer('quantity')->default(0)->after('unit');
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('quantity');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_sklad', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['quantity', 'warehouse_id']);
        });
    }
};
