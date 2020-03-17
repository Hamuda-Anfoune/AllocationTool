<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ta_preferences', function (Blueprint $table) {
            $table->string('preference_id', 45)->primary();
            $table->string('ta_user_id', 50);
            $table->mediumInteger('max_hours')->nullable();
            $table->mediumInteger('max_modules')->nullable();
            $table->string('academic_year', 15);
            $table->mediumInteger('semester');
            $table->boolean('have_tiear4_visa');

            // RELATIONSHIPS
            $table->foreign('ta_user_id')->references('user_id')->on('users'); //relationship  one to one between users & ta_preferences tables
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ta_preferences');
    }
}
