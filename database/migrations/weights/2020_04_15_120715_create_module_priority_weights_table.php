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
            $table->timestamps();
        });

        DB::table('module_priority_weights')->insert(
            array(
                [
                    'type' => 'default',
                    'module_weight_1' => 20,
                    'module_weight_2' => 12,
                    'module_weight_3' => 8,
                    'module_weight_4' => 4,
                    'module_weight_5' => 4,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'type' => 'current',
                    'module_weight_1' => 20,
                    'module_weight_2' => 12,
                    'module_weight_3' => 8,
                    'module_weight_4' => 4,
                    'module_weight_5' => 4,
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
