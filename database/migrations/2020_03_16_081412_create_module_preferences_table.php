<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulePreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_preferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('module_id', 15);
            $table->string('convenor_user_id', 50);
            $table->mediumInteger('no_of_assistants')->nullable();
            $table->mediumInteger('no_of_contact_hours');
            $table->mediumInteger('no_of_marking_hours');
            $table->string('academic_year', 15);
            $table->mediumInteger('semester');

            //REALTIONSHIPS
            $table->foreign('module_id')->references('module_id')->on('modules');
            $table->foreign('convenor_user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_preferences');
    }
}
