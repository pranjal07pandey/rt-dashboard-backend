<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentDcoketTimerAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_dcoket_timer_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sent_docket_id');
            $table->integer('type');
            $table->integer('timer_id')->unsigned();
            $table->foreign('timer_id')->references('id')->on('timers');
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
        Schema::dropIfExists('sent_dcoket_timer_attachments');
    }
}
