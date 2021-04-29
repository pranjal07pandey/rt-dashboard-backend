<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSentInvoiceValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_sent_invoice_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_sent_invoice_id')->unsigned();
            $table->foreign('email_sent_invoice_id')->references('id')->on('email_sent_invoices');
            $table->integer('invoice_field_id')->unsigned();
            $table->foreign('invoice_field_id')->references('id')->on('invoice_fields');
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
        Schema::dropIfExists('email_sent_invoice_values');
    }
}
