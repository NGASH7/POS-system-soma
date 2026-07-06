<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('returned_from_sale_id')->nullable()->constrained('sales')->onDelete('set null');
            $table->string('return_reason')->nullable();
            $table->enum('return_type', ['return', 'exchange'])->nullable();
            $table->decimal('return_amount', 10, 2)->default(0);
            $table->string('return_receipt_no')->nullable()->unique();
            $table->boolean('is_return')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['returned_from_sale_id']);
            $table->dropColumn([
                'returned_from_sale_id',
                'return_reason',
                'return_type',
                'return_amount',
                'return_receipt_no',
                'is_return'
            ]);
        });
    }
};