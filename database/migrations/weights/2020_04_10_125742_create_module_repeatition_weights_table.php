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
            $table->float('repeated_times_1');
            $table->float('repeated_times_2');
            $table->float('repeated_times_3');
            $table->float('repeated_times_4');
            $table->float('repeated_times_5');
            $table->timestamps();
        });

        // Inseert default data
        DB::table('module_repeatition_weights')->insert(
            array([
                    'type' => 'default',
                    'repeated_times_1' => 50,
                    'repeated_times_2' => 30,
                    'repeated_times_3' => 20,
                    'repeated_times_4' => 10,
                    'repeated_times_5' => 10,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')],
                [
                    'type' => 'current',
                    'repeated_times_1' => 50,
                    'repeated_times_2' => 30,
                    'repeated_times_3' => 20,
                    'repeated_times_4' => 10,
                    'repeated_times_5' => 10,
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
