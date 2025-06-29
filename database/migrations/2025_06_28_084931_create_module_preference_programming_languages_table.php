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
        Schema::create('module_preference_programming_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_preference_id')
                ->constrained('module_preferences')
                ->onDelete('cascade')
                ->name('mod_pref_prog_lang_mod_pref_id_fk');
            $table->foreignId('programming_language_id')
                ->constrained('programming_languages')
                ->onDelete('cascade')
                ->name('mod_pref_prog_lang_prog_lang_id_fk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_preference_programming_languages');
    }
};
