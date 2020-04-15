<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateModulePriorityWeightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_priority_weights', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 15); // type of entry: default || current
            $table->float('module_weight_1');
            $table->float('module_weight_2');
            $table->float('module_weight_3');
            $table->float('module_weight_4');
            $table->float('module_weight_5');
            $table->float('module_weight_6');
            $table->float('module_weight_7');
            $table->float('module_weight_8');
            $table->float('module_weight_9');
            $table->float('module_weight_10');
            $table->timestamps();
        });

        DB::table('module_priority_weights')->insert(
            array(
                [
                    'type' => 'default',
                    'module_weight_1' => 27,
                    'module_weight_2' => 23,
                    'module_weight_3' => 17,
                    'module_weight_4' => 13,
                    'module_weight_5' => 10,
                    'module_weight_6' => 8,
                    'module_weight_7' => 4,
                    'module_weight_8' => 4,
                    'module_weight_9' => 2,
                    'module_weight_10' => 2,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'type' => 'current',
                    'module_weight_1' => 27,
                    'module_weight_2' => 23,
                    'module_weight_3' => 17,
                    'module_weight_4' => 13,
                    'module_weight_5' => 10,
                    'module_weight_6' => 8,
                    'module_weight_7' => 4,
                    'module_weight_8' => 4,
                    'module_weight_9' => 2,
                    'module_weight_10' => 2,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
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
        Schema::dropIfExists('module_priority_weights');
    }
}
