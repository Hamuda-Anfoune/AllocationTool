<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaAllocationDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ta_allocation_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('allocation_id', 50);
            $table->string('ta_id', 50);
            $table->smallInteger('contact_hours');
            $table->smallInteger('marking_hours');
            $table->string('academic_year', 15);
            $table->timestamps();

            //REALTIONSHIPS
            $table->foreign('ta_id')->references('email')->on('users');
            $table->foreign('academic_year')->references('year')->on('academic_years');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ta_allocation_data');
    }
}
