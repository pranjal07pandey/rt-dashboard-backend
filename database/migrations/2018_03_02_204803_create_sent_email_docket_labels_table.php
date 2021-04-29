<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentEmailDocketLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_email_docket_labels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('docket_label_id')->unsigned();
            $table->foreign('docket_label_id')->references('id')->on('docket_labels');
            $table->integer('email_sent_docket_id')->unsigned();
            $table->foreign('email_sent_docket_id')->references('id')->on('email_sent_dockets');
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
        Schema::dropIfExists('sent_email_docket_labels');
    }
}
