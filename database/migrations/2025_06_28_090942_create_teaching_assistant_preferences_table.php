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
        Schema::create('teaching_assistant_preferences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('teaching_assistant_id')->constrained('teaching_assistants')->onDelete('cascade');
            $table->string('academic_year_id', 20);
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');

            $table->integer('max_modules')->default(0);
            $table->integer('max_weekly_contact_hours')->default(0);
            $table->integer('max_semester_marking_hours')->default(0);
            $table->boolean('is_on_student_visa')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_assistant_preferences');
    }
};
