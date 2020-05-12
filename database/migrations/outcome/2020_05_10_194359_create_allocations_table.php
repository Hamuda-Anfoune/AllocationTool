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
            $table->bigIncrements('id');
            $table->string('allocation_id', 50);
            $table->string('academic_year', 15);
            $table->string('ta_id', 50);
            $table->string('module_id', 15);
            $table->string('creator_email', 50);
            $table->timestamps();

            //REALTIONSHIPS
            $table->foreign('ta_id')->references('email')->on('users');
            $table->foreign('module_id')->references('module_id')->on('modules');
            $table->foreign('academic_year')->references('year')->on('academic_years');
            $table->foreign('creator_email')->references('email')->on('users');
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
