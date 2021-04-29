<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentInvoicePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_invoice_payment_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sent_invoice_id')->unsigned();
            $table->foreign('sent_invoice_id')->references('id')->on('sent_invoices');

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
        Schema::dropIfExists('sent_invoice_payment_details');
    }
}
