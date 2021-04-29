<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentInvoiceLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_invoice_labels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_label_id')->unsigned();
            $table->foreign('invoice_label_id')->references('id')->on('invoice__labels');
            $table->integer('sent_invoice_id')->unsigned();
            $table->foreign('sent_invoice_id')->references('id')->on('sent_invoices');
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
        Schema::dropIfExists('sent_invoice_labels');
    }
}
