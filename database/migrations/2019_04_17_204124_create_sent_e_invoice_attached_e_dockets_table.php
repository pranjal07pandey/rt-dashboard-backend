<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentEInvoiceAttachedEDocketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_e_invoice_attached_e_dockets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sent_email_invoice_id')->unsigned();
            $table->foreign('sent_email_invoice_id')->references('id')->on('email_sent_invoices');
            $table->integer('sent_email_docket_id')->unsigned();
            $table->foreign('sent_email_docket_id')->references('id')->on('email_sent_dockets');
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
        Schema::dropIfExists('sent_e_invoice_attached_e_dockets');
    }
}
