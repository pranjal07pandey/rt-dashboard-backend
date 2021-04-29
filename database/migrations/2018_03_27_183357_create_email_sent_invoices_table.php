<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSentInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_sent_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned();
            $table->foreign('invoice_id')->references('id')->on('invoices');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');

            $table->text('abn');
            $table->text('sender_name');
            $table->text('company_address');
            $table->text('company_name');
            $table->text('	company_logo');

            $table->integer('receiver_user_id')->unsigned();
            $table->foreign('receiver_user_id')->references('id')->on('email_users');
            $table->string('receiver_full_name')->nullable();
            $table->string('receiver_company_name')->nullable();
            $table->string('receiver_company_address')->nullable();

            $table->decimal('amount',19,4);
            $table->decimal('gst',19,2);
            $table->boolean('isDocketAttached');
            $table->string('hashKey');
            $table->boolean('status');

            $table->string('xero_invoice_id')->default(0);
            $table->integer('folder_status')->default(0);
            $table->integer('company_invoice_id');
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
        Schema::dropIfExists('email_sent_invoices');
    }
}
