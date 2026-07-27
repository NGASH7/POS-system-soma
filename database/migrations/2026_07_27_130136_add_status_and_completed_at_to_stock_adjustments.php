<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_adjustments', 'status')) {
                $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending')->after('adjustment_date');
            }
            if (!Schema::hasColumn('stock_adjustments', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            if (Schema::hasColumn('stock_adjustments', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('stock_adjustments', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
        });
    }
};