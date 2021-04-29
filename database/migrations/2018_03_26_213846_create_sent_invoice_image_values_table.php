<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentInvoiceImageValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_invoice_image_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sent_invoice_value_id')->unsigned();
            $table->foreign('sent_invoice_value_id')->references('id')->on('sent_invoice_values');
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
        Schema::dropIfExists('sent_invoice_image_values');
    }
}
