<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->string('module_id', 15)->primary();
            $table->string('module_name', 150);
            $table->string('convenor_email', 50);

            //REALTIONSHIPS
            $table->foreign('convenor_email')->references('email')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign keys before table
        Schema::table('modules', function(Blueprint $table){
            $table->dropForeign('convenor_email');
        });
        Schema::dropIfExists('modules');
    }
}
