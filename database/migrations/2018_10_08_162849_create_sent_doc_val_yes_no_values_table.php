<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentDocValYesNoValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_doc_val_yes_no_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sent_docket_value_id')->unsigned();
            $table->foreign('sent_docket_value_id')->references('id')->on('sent_dockets_values');
            $table->integer('yes_no_docket_field_id')->unsigned();
            $table->foreign('yes_no_docket_field_id')->references('id')->on('yes_no_dockets_fields');
            $table->string('label');
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
        Schema::dropIfExists('sent_doc_val_yes_no_values');
    }
}
