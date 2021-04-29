<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXeroInvoiceValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xero_invoice_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('xero_field_id')->unsigned();
            $table->foreign('xero_field_id')->references('id')->on('xero_fields');
            $table->integer('invoice_xero_setting_id')->unsigned();
            $table->foreign('invoice_xero_setting_id')->references('id')->on('invoice_xero_settings');
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
        Schema::dropIfExists('xero_invoice_values');
    }
}
