<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSentInvoiceLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_sent_invoice_labels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_label_id')->unsigned();
            $table->foreign('invoice_label_id')->references('id')->on('invoice__labels');
            $table->integer('email_sent_id')->unsigned();
            $table->foreign('email_sent_id')->references('id')->on('email_sent_invoices');
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
        Schema::dropIfExists('email_sent_invoice_labels');
    }
}
