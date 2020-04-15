<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModuleRankOrderListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_rank_order_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('module_id', 15);
            $table->string('academic_year', 15);
            $table->string('ta_email');
            $table->float('total_weight');
            $table->float('did_before_weight');
            $table->timestamps();

            //RELATIPNSHIPS
            $table->foreign('module_id')->references('module_id')->on('modules');
            $table->foreign('ta_email')->references('email')->on('users'); //relationship  one to one between users & ta_preferences tables
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
        Schema::dropIfExists('module_rank_order_lists');
    }
}
