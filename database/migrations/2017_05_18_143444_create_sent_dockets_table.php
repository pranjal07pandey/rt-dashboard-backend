<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentDocketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_dockets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('docket_id')->unsigned();
            $table->foreign('docket_id')->references('id')->on('dockets');
            $table->integer('sender_company_id')->unsigned();
            $table->foreign('sender_company_id')->references('id')->on('companies');
            $table->boolean('status');
            $table->boolean('invoiceable');
            $table->timestamps();
            //            $table->integer('receiver_user_id')->unsigned()
            //            $table->foreign('receiver_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sent_dockets');
    }
}
