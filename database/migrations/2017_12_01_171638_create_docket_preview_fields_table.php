<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocketPreviewFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docket_preview_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('docket_id')->unsigned();
            $table->foreign('docket_id')->references('id')->on('dockets');
            $table->integer('docket_field_id')->unsigned();
            $table->integer('order');
            $table->foreign('docket_field_id')->references('id')->on('docket_fields');
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
        Schema::dropIfExists('docket_preview_fields');
    }
}
