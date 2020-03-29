<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ta_preferences', function (Blueprint $table) {
            $table->string('preference_id', 45)->primary();
            $table->string('ta_email', 50);
            $table->mediumInteger('max_modules')->nullable();
            $table->mediumInteger('max_contact_hours')->nullable();
            $table->mediumInteger('max_marking_hours')->nullable();
            $table->string('academic_year', 15);
            $table->mediumInteger('semester');
            $table->boolean('have_tier4_visa')->default(false);
            $table->timestamps();

            // RELATIONSHIPS
            $table->foreign('ta_email')->references('email')->on('users'); //relationship  one to one between users & ta_preferences tables
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ta_preferences', function(Blueprint $table){
            // $table->dropForeign(['ta_email']);
            $table->dropPrimary('preference_id'); // WOULD NOT WORK WITHOUT DROPPING FOREIGN KEY IN ta_module_choices
            $table->dropForeign('ta_preferences_ta_email_foreign');
            $table->dropIndex('ta_preferences_ta_email_foreign');
        });

        Schema::dropIfExists('ta_preferences');
    }
}
