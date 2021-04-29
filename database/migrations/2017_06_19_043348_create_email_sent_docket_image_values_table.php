<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSentDocketImageValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_sent_docket_image_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sent_docket_value_id')->unsigned();
            $table->foreign('sent_docket_value_id')->references('id')->on('email_sent_docket_values');
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
        Schema::dropIfExists('email_sent_docket_image_values');
    }
}
