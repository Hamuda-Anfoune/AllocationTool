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
        Schema::create('module_convenors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->foreignId('convenor_id')->constrained('users')->onDelete('cascade');
            $table->string('academic_year_id', 20);
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('restrict');
            
            $table->timestamps();

            $table->unique(['module_id', 'convenor_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_convenors');
    }
};
