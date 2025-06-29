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
        Schema::create('module_preferences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->foreignId('convenor_id')->constrained('convenors')->onDelete('cascade');
            $table->string('academic_year_id', 20);
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('restrict');
            
            $table->integer('assistants_required')->default(0);
            $table->integer('contact_hours')->default(0);
            $table->integer('marking_hours')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_preferences');
    }
};
