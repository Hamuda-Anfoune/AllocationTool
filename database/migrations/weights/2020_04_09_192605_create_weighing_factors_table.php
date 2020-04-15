<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateWeighingFactorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weighing_factors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 15); // type of entry: default || current
            $table->float('factor_1');
            $table->float('factor_2');
            $table->float('factor_3');
            $table->float('factor_4');
            $table->float('factor_5');
            $table->timestamps();
        });

        // Insert some stuff
        DB::table('weighing_factors')->insert(
            array(
                [
                    'type' => 'default',
                    'factor_1' => 1,
                    'factor_2' => 1,
                    'factor_3' => 2,
                    'factor_4' => 3,
                    'factor_5' => 5,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'type' => 'current',
                    'factor_1' => 1,
                    'factor_2' => 1,
                    'factor_3' => 2,
                    'factor_4' => 3,
                    'factor_5' => 5,
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
        Schema::dropIfExists('weighing_factors');
    }
}
