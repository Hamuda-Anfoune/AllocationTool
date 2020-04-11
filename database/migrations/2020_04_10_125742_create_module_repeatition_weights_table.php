<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateModuleRepeatitionWeightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_repeatition_weights', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 15); // type of entry: default || current
            $table->float('1_time_weight');
            $table->float('2_time_weight');
            $table->float('3_time_weight');
            $table->float('4_time_weight');
            $table->float('5_time_weight');
            $table->timestamps();
        });

        // Inseert default data
        DB::table('module_repeatition_weights')->insert(
            array([
                    'type' => 'default',
                    '1_time_weight' => 50,
                    '2_time_weight' => 30,
                    '3_time_weight' => 20,
                    '4_time_weight' => 10,
                    '5_time_weight' => 10,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')],
                [
                    'type' => 'current',
                    '1_time_weight' => 50,
                    '2_time_weight' => 30,
                    '3_time_weight' => 20,
                    '4_time_weight' => 10,
                    '5_time_weight' => 10,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')]
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_repeatition_weights');
    }
}
