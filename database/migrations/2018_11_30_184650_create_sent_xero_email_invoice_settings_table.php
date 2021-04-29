<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentXeroEmailInvoiceSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_xero_email_invoice_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('xero_field_id')->unsigned();
            $table->foreign('xero_field_id')->references('id')->on('xero_fields');
            $table->integer('email_sent_invoice_id')->unsigned();
            $table->foreign('email_sent_invoice_id')->references('id')->on('email_sent_invoices');
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
        Schema::dropIfExists('sent_xero_email_invoice_settings');
    }
}
