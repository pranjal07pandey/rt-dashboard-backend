<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSentDocManualTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_sent_doc_manual_timers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sent_docket_value_id')->unsigned();
            $table->foreign('sent_docket_value_id')->references('id')->on('email_sent_docket_values');
            $table->integer('docket_manual_timer_id')->unsigned();
            $table->foreign('docket_manual_timer_id')->references('id')->on('docket_manual_timers');
            $table->string('value');
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
        Schema::dropIfExists('email_sent_doc_manual_timers');
    }
}
