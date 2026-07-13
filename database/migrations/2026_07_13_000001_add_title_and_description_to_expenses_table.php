<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'title')) {
                $table->string('title')->nullable()->after('reference_no');
            }
            if (!Schema::hasColumn('expenses', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
        });

        if (Schema::hasColumn('expenses', 'note')) {
            DB::table('expenses')
                ->whereNull('description')
                ->whereNotNull('note')
                ->update(['description' => DB::raw('note')]);
        }
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('expenses', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
