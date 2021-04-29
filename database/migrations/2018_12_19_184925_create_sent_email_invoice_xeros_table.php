<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentEmailInvoiceXerosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_email_invoice_xeros', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sent_email_invoice_id')->unsigned();
            $table->foreign('sent_email_invoice_id')->references('id')->on('email_sent_invoices');
            $table->integer('company_xero_id')->unsigned();
            $table->foreign('company_xero_id')->references('id')->on('company_xeros');
            $table->string('xero_invoice_id');
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
        Schema::dropIfExists('sent_email_invoice_xeros');
    }
}
