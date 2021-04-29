<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentDocketManualTimerBreaksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_docket_manual_timer_breaks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sent_docket_value_id')->unsigned();
            $table->foreign('sent_docket_value_id')->references('id')->on('sent_dockets_values');
            $table->integer('manual_timer_break_id')->unsigned();
            $table->foreign('manual_timer_break_id')->references('id')->on('docket_manual_timer_breaks');
            $table->string('value');
            $table->string('label');
            $table->string('reason');
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
        Schema::dropIfExists('sent_docket_manual_timer_breaks');
    }
}
