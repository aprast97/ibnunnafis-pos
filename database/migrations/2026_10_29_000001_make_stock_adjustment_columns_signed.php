<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('current_stock', 12, 2)->default(0)->change();
        });

        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->decimal('system_stock', 12, 2)->change();
            $table->decimal('physical_stock', 12, 2)->change();
            $table->decimal('difference', 12, 2)->change();
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->decimal('quantity', 12, 2)->change();
            $table->decimal('stock_before', 12, 2)->change();
            $table->decimal('stock_after', 12, 2)->change();
        });
    }

    public function down(): void
    {
        // These columns must remain signed so stock reductions can be recorded.
    }
};
