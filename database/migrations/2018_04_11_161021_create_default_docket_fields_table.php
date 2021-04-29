<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultDocketFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_docket_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('default_docket_id')->unsigned();
            $table->foreign('default_docket_id')->references('id')->on('default_dockets');
            $table->integer('docket_field_category_id');
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
        Schema::dropIfExists('default_docket_fields');
    }
}
