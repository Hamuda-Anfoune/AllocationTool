<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // $table->bigIncrements('id');
            // $table->string('name');
            // $table->string('email')->unique();
            // $table->timestamp('email_verified_at')->nullable();
            // $table->string('password');
            // $table->rememberToken();
            // $table->timestamps();
            $table->string('email', 50)->primary();
            // $table->string('user_id', 100)->unique(); // USE AS KEY BECAUSE UNIVERSITITES USE UNIQUE EMAILS
            $table->string('name');
            $table->string('account_type_id',10); // 10: is the length of the string
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            // RELATIONSHIP
            $table->foreign('email')->references('email')->on('university_users'); //relationship  one to one between users & university_users tables
            $table->foreign('account_type_id')->references('account_type_id')->on('account_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
