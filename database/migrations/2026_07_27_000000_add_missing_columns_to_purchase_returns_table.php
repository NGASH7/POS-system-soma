<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair databases where the original purchase_returns migration was run
     * before its return-detail columns were added to the migration file.
     */
    public function up(): void
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_returns', 'return_no')) {
                $table->string('return_no')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('purchase_returns', 'purchase_id')) {
                $table->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete()->after('return_no');
            }

            if (! Schema::hasColumn('purchase_returns', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete()->after('purchase_id');
            }

            if (! Schema::hasColumn('purchase_returns', 'outlet_id')) {
                $table->foreignId('outlet_id')->nullable()->constrained('outlets')->nullOnDelete()->after('supplier_id');
            }

            if (! Schema::hasColumn('purchase_returns', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('outlet_id');
            }

            if (! Schema::hasColumn('purchase_returns', 'return_date')) {
                $table->date('return_date')->nullable()->after('user_id');
            }

            if (! Schema::hasColumn('purchase_returns', 'status')) {
                $table->string('status')->default('completed')->after('return_date');
            }

            if (! Schema::hasColumn('purchase_returns', 'refund_status')) {
                $table->string('refund_status')->default('refunded')->after('status');
            }

            if (! Schema::hasColumn('purchase_returns', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0)->after('refund_status');
            }

            if (! Schema::hasColumn('purchase_returns', 'notes')) {
                $table->text('notes')->nullable()->after('total_amount');
            }
        });
    }

    public function down(): void
    {
        // Intentionally left as a no-op: this migration may have repaired a
        // legacy table, or found that a fresh install already had the columns.
        // Dropping them on rollback would risk removing the base schema.
    }
};
