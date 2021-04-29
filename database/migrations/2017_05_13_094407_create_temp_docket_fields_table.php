<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempDocketFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_docket_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('docket_id')->unsigned();
            $table->foreign('docket_id')->references('id')->on('temp_dockets');
            $table->integer('docket_field_category_id')->unsigned();
            $table->foreign('docket_field_category_id')->references('id')->on('docket_filed_categories');
            $table->integer('order');
            $table->string('label');
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
        Schema::dropIfExists('temp_docket_fields');
    }
}
