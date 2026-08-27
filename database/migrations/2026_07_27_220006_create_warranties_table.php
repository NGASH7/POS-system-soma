<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->string('warranty_no')->unique();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('outlet_id')->constrained();
            
            $table->date('purchase_date');
            $table->date('expiry_date');
            $table->string('serial_number')->nullable();
            $table->string('batch_number')->nullable();
            
            $table->enum('status', ['active', 'expired', 'claimed', 'replaced', 'void'])->default('active');
            $table->enum('warranty_type', ['manufacturer', 'store', 'extended']);
            $table->integer('warranty_duration_months')->default(12);
            
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};