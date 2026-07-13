<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfers', 'reference_no')) {
                $table->string('reference_no')->unique()->after('id');
            }
            if (!Schema::hasColumn('stock_transfers', 'from_outlet_id')) {
                $table->foreignId('from_outlet_id')->constrained('outlets')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('stock_transfers', 'to_outlet_id')) {
                $table->foreignId('to_outlet_id')->constrained('outlets')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('stock_transfers', 'user_id')) {
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('stock_transfers', 'status')) {
                $table->enum('status', ['completed', 'cancelled'])->default('completed');
            }
            if (!Schema::hasColumn('stock_transfers', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('stock_transfers', 'transferred_at')) {
                $table->timestamp('transferred_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $columns = ['reference_no', 'from_outlet_id', 'to_outlet_id', 'user_id', 'status', 'notes', 'transferred_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('stock_transfers', $column)) {
                    if (in_array($column, ['from_outlet_id', 'to_outlet_id', 'user_id'])) {
                        $table->dropForeign([$column]);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
