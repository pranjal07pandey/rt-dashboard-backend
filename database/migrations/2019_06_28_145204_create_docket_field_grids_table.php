<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocketFieldGridsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docket_field_grids', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('docket_field_id')->unsigned();
            $table->foreign('docket_field_id')->references('id')->on('docket_fields');
            $table->integer('docket_field_category_id')->unsigned();
            $table->foreign('docket_field_category_id')->references('id')->on('docket_filed_categories');
            $table->integer('order')->nullable(0);
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
        Schema::dropIfExists('docket_field_grids');
    }
}
