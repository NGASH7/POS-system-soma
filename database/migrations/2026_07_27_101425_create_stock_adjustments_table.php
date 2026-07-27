<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_no')->unique();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('outlet_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('quantity');
            $table->enum('type', ['increase', 'decrease']);
            $table->enum('reason', [
                'spoilage',
                'damage',
                'theft',
                'return',
                'correction',
                'expired',
                'lost',
                'other'
            ]);
            $table->text('reason_description')->nullable();
            $table->decimal('old_stock', 10, 2)->default(0);
            $table->decimal('new_stock', 10, 2)->default(0);
            $table->date('adjustment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};