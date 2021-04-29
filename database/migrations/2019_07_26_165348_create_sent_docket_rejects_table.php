<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentDocketRejectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_docket_rejects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sent_docket_id')->unsigned();
            $table->foreign('sent_docket_id')->references('id')->on('sent_dockets');
            $table->text('explanation');
            $table->integer('user_id');
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
        Schema::dropIfExists('sent_docket_rejects');
    }
}
