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
        $tables = ['users', 'products', 'sales', 'customers', 'categories', 'returns'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableSchema) {
                $tableSchema->unsignedBigInteger('outlet_id')->nullable()->after('id');
                // We're making it nullable first so it doesn't fail on existing data
                $tableSchema->foreign('outlet_id')->references('id')->on('outlets')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['users', 'products', 'sales', 'customers', 'categories', 'returns'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableSchema) {
                $tableSchema->dropForeign(['outlet_id']);
                $tableSchema->dropColumn('outlet_id');
            });
        }
    }
};
