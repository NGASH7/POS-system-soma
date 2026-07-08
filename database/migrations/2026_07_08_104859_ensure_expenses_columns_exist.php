<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            }
            if (!Schema::hasColumn('expenses', 'receipt_image')) {
                $table->string('receipt_image')->nullable();
            }
            if (!Schema::hasColumn('expenses', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false);
            }
            if (!Schema::hasColumn('expenses', 'recurring_frequency')) {
                $table->string('recurring_frequency')->nullable();
            }
            if (!Schema::hasColumn('expenses', 'recurring_end_date')) {
                $table->date('recurring_end_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $columns = ['status', 'receipt_image', 'is_recurring', 'recurring_frequency', 'recurring_end_date'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('expenses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};