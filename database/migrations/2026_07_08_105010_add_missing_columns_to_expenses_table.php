<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Add columns one by one, checking if they exist first
            if (!Schema::hasColumn('expenses', 'vendor')) {
                $table->string('vendor')->nullable()->after('expense_date');
            }
            if (!Schema::hasColumn('expenses', 'payment_method')) {
                $table->string('payment_method')->default('cash')->after('expense_date');
            }
            if (!Schema::hasColumn('expenses', 'receipt_image')) {
                $table->string('receipt_image')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('expenses', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('receipt_image');
            }
            if (!Schema::hasColumn('expenses', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('status');
            }
            if (!Schema::hasColumn('expenses', 'recurring_frequency')) {
                $table->string('recurring_frequency')->nullable()->after('is_recurring');
            }
            if (!Schema::hasColumn('expenses', 'recurring_end_date')) {
                $table->date('recurring_end_date')->nullable()->after('recurring_frequency');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $columns = ['vendor', 'payment_method', 'receipt_image', 'status', 'is_recurring', 'recurring_frequency', 'recurring_end_date'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('expenses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};