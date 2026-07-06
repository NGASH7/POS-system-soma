<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('total_credit', 10, 2)->default(0)->after('total_spent');
            $table->decimal('available_credit', 10, 2)->default(0)->after('total_credit');
            $table->integer('credit_limit')->default(0)->after('available_credit');
            $table->date('credit_approved_until')->nullable()->after('credit_limit');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['total_credit', 'available_credit', 'credit_limit', 'credit_approved_until']);
        });
    }
};