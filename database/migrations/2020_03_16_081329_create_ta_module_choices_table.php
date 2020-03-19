<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaModuleChoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ta_module_choices', function (Blueprint $table) {
            $table->bigIncrements('field_id');
            $table->string('preference_id', 45);
            $table->string('ta_user_id');
            $table->string('module_id', 15);
            $table->smallInteger('priority');
            $table->boolean('did_before'); // used ti=inyInteger in the test

            // RELATIONSHIPS
            $table->foreign('preference_id')->references('preference_id')->on('ta_preferences'); //relationship  one to one between users & university_users tables
            $table->foreign('ta_user_id')->references('user_id')->on('users');
            $table->foreign('module_id')->references('module_id')->on('modules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ta_module_choices');
    }
}
