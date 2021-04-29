<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentInvoiceDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_invoice_descriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sent_invoice_id')->unsigned();
            $table->foreign('sent_invoice_id')->references('id')->on('sent_invoices');
            $table->text('description');
            $table->decimal('amount',19,4);
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
        Schema::dropIfExists('sent_invoice_descriptions');
    }
}
