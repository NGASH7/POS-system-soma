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
        Schema::table('sales', function (Blueprint $table) {
            // Add mpesa_receipt column after payment_method
            $table->string('mpesa_receipt')->nullable()->after('payment_method');
            
            // Add additional M-Pesa related fields if needed
            $table->string('mpesa_checkout_request_id')->nullable()->after('mpesa_receipt');
            $table->string('mpesa_merchant_request_id')->nullable()->after('mpesa_checkout_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'mpesa_receipt',
                'mpesa_checkout_request_id',
                'mpesa_merchant_request_id'
            ]);
        });
    }
};