<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentDocketAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_docket_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->text('url');
            $table->text('document_name');
            $table->integer('sent_dockets_value_id')->unsigned();
            $table->foreign('sent_dockets_value_id')->references('id')->on('sent_dockets_values');
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
        Schema::dropIfExists('sent_docket_attachments');
    }
}
