<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentEmailDocketInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_email_docket_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_sent_docket_id')->unsigned();
            $table->foreign('email_sent_docket_id')->references('id')->on('email_sent_dockets');
            $table->integer('email_sent_docket_value_id')->unsigned();
            $table->foreign('email_sent_docket_value_id')->references('id')->on('email_sent_docket_values');
            $table->integer('type');
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
        Schema::dropIfExists('sent_email_docket_invoices');
    }
}
