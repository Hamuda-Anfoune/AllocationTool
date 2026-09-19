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
        Schema::table('module_rank_order_lists', function (Blueprint $table) {
            $table->index(['academic_year', 'module_id', 'ta_email'], 'module_rank_order_lists_year_module_ta_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('module_rank_order_lists', function (Blueprint $table) {
            $table->dropIndex('module_rank_order_lists_year_module_ta_index');
        });
    }
};
