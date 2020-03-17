<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocations', function (Blueprint $table) {
            $table->string('allocation_id', 15)->primary();
            $table->string('academic_year', 15);
            $table->mediumInteger('semester');
            $table->string('module_id', 15);
            $table->string('ta_user_id', 50);
            $table->string('creator_user_id', 50);
            $table->timestamps();

            //REALTIONSHIPS
            $table->foreign('ta_user_id')->references('user_id')->on('users');
            $table->foreign('module_id')->references('module_id')->on('modules');
            $table->foreign('creator_user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allocations');
    }
}
