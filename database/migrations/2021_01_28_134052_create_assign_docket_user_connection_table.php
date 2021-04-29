<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignDocketUserConnectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_docket_user_connection', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assign_docket_id')->unsigned();
            $table->foreign('assign_docket_id')->references('id')->on('assign_docket_user');
            $table->integer('user_id')->nullable()->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('machine_id')->nullable()->unsigned();
            $table->foreign('machine_id')->references('id')->on('machines');
            $table->integer('docket_id')->unsigned();
            $table->foreign('docket_id')->references('id')->on('dockets');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assign_docket_user_connection');
    }
}
