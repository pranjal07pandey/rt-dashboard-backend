<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SentDocketLabels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_docket_labels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('docket_label_id')->unsigned();
            $table->foreign('docket_label_id')->references('id')->on('docket_labels');
            $table->integer('sent_docket_id')->unsigned();
            $table->foreign('sent_docket_id')->references('id')->on('sent_dockets');
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
        Schema::dropIfExists('sent_docket_labels');

    }
}
