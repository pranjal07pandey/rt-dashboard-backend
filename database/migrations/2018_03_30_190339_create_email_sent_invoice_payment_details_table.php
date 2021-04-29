<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSentInvoicePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_sent_invoice_payment_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_sent_invoice_id')->unsigned();
            $table->foreign('email_sent_invoice_id')->references('id')->on('email_sent_invoices');

            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('bank_name');
            $table->string('account_name');
            $table->string('bsb_number');
            $table->string('account_number');
            $table->string('instruction');
            $table->string('additional_information');
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
        Schema::dropIfExists('email_sent_invoice_payment_details');
    }
}
