<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSentDocketRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_sent_docket_recipients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_sent_docket_id')->unsigned();
            $table->foreign('email_sent_docket_id')->references('id')->on('email_sent_dockets');
            $table->integer('email_user_id')->unsigned();
            $table->foreign('email_user_id')->references('id')->on('email_users');
            $table->boolean('approval');
            $table->string('hashKey');
            $table->string('receiver_full_name')->nullable();
            $table->string('receiver_company_name')->nullable();
            $table->string('receiver_company_address')->nullable();
            $table->integer('status');
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
        Schema::dropIfExists('email_sent_docket_recipients');
    }
}
