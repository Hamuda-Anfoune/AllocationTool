<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaLanguageChoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ta_language_choices', function (Blueprint $table) {
            $table->bigIncrements('field_id');
            $table->string('preference_id', 45);
            $table->string('language_id', 10);
            $table->timestamps();

            // RELATIONSHIPS
            $table->foreign('preference_id')->references('preference_id')->on('ta_preferences');
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
        Schema::dropIfExists('ta_language_choices');
    }
}
