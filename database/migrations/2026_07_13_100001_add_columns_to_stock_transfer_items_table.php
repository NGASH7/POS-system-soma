<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transfer_items', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfer_items', 'stock_transfer_id')) {
                $table->foreignId('stock_transfer_id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('stock_transfer_items', 'product_id')) {
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('stock_transfer_items', 'destination_product_id')) {
                $table->foreignId('destination_product_id')->nullable()->constrained('products')->nullOnDelete();
            }
            if (!Schema::hasColumn('stock_transfer_items', 'quantity')) {
                $table->unsignedInteger('quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_transfer_items', function (Blueprint $table) {
            $columns = ['stock_transfer_id', 'product_id', 'destination_product_id', 'quantity'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('stock_transfer_items', $column)) {
                    if (in_array($column, ['stock_transfer_id', 'product_id', 'destination_product_id'])) {
                        $table->dropForeign([$column]);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
