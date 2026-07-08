<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if outlet_id already exists before adding
        if (!Schema::hasColumn('users', 'outlet_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('outlet_id')->nullable()->after('id')->constrained()->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('products', 'outlet_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('outlet_id')->nullable()->constrained()->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('sales', 'outlet_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->foreignId('outlet_id')->nullable()->constrained()->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('customers', 'outlet_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->foreignId('outlet_id')->nullable()->constrained()->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('categories', 'outlet_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->foreignId('outlet_id')->nullable()->constrained()->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('returns', 'outlet_id')) {
            Schema::table('returns', function (Blueprint $table) {
                $table->foreignId('outlet_id')->nullable()->constrained()->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        // Drop columns if they exist
        $tables = ['users', 'products', 'sales', 'customers', 'categories', 'returns'];
        
        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'outlet_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->dropForeign(['outlet_id']);
                    $table->dropColumn('outlet_id');
                });
            }
        }
    }
};