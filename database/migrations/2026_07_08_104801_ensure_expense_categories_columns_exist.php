<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            // Only add columns if they don't already exist
            if (!Schema::hasColumn('expense_categories', 'color')) {
                $table->string('color')->default('#6b7280')->after('description');
            }
            if (!Schema::hasColumn('expense_categories', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            if (Schema::hasColumn('expense_categories', 'color')) {
                $table->dropColumn('color');
            }
            if (Schema::hasColumn('expense_categories', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};