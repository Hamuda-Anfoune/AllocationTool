<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsedLangaugesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('used_langauges', function (Blueprint $table) {
            $table->bigIncrements('field_id')->primary();
            $table->string('module_id', 15);
            $table->string('language_id', 10);

            //REALTIONSHIPS
            $table->foreign('module_id')->references('module_id')->on('modules');
            $table->foreign('language_id')->references('language_id')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('used_langauges');
    }
}
