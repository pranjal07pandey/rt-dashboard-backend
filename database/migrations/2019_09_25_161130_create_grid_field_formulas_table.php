<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGridFieldFormulasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grid_field_formulas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('docket_field_grid_id')->unsigned();
            $table->foreign('docket_field_grid_id')->references('id')->on('docket_field_grids');
            $table->integer('user_id');
            $table->text('formula');

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
        Schema::dropIfExists('grid_field_formulas');
    }
}
