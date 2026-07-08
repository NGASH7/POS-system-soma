<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('contact_info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add outlet_id to all relevant tables
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('outlet_id')->nullable()->constrained()->onDelete('set null');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('outlet_id')->nullable()->constrained()->onDelete('set null');
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('outlet_id')->nullable()->constrained()->onDelete('set null');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('outlet_id')->nullable()->constrained()->onDelete('set null');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('outlet_id')->nullable()->constrained()->onDelete('set null');
        });
        Schema::table('returns', function (Blueprint $table) {
            $table->foreignId('outlet_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });
        Schema::table('returns', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });
        Schema::dropIfExists('outlets');
    }
};