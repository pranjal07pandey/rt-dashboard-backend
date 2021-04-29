<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSentInvoiceImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_sent_invoice_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_sent_invoice_value_id')->unsigned();
            $table->foreign('email_sent_invoice_value_id')->references('id')->on('email_sent_invoice_values');
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
        Schema::dropIfExists('email_sent_invoice_images');
    }
}
