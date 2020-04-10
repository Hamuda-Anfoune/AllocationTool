<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateLanguageWeightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('language_weights', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 15); // type of entry: default || current
            $table->float('language_weight_1');
            $table->float('language_weight_2');
            $table->float('language_weight_3');
            $table->float('language_weight_4');
            $table->float('language_weight_5');
            $table->timestamps();
        });

        // Inseert default data
        DB::table('language_weights')->insert(
            array(
                'type' => 'default',
                'language_weight_1' => 50,
                'language_weight_2' => 30,
                'language_weight_3' => 20,
                'language_weight_4' => 10,
                'language_weight_5' => 10,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
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
        Schema::dropIfExists('language_weights');
    }
}
