<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentXeroInvoiceSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_xero_invoice_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('xero_field_id')->unsigned();
            $table->foreign('xero_field_id')->references('id')->on('xero_fields');
            $table->integer('sent_invoice_xero_id')->unsigned();
            $table->foreign('sent_invoice_xero_id')->references('id')->on('sent_invoice_xeros');
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
        Schema::dropIfExists('sent_xero_invoice_settings');
    }
}
