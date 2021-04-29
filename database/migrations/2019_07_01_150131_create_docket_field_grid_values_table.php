<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocketFieldGridValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docket_field_grid_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('docket_id');
            $table->integer('is_email_docket');
            $table->integer('docket_field_grid_id')->unsigned();
            $table->foreign('docket_field_grid_id')->references('id')->on('docket_field_grids');
            $table->string('value');
            $table->integer('index');
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
        Schema::dropIfExists('docket_field_grid_values');
    }
}
