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
        Schema::create('teaching_assistant_preference_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_assistant_preference_id')
                ->constrained('teaching_assistant_preferences')
                ->onDelete('cascade')
                ->name('ta_pref_mods_ta_pref_id_fk');
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->integer('priority');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_assistant_preference_modules');
    }
};
