<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocketGridPrefillersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docket_grid_prefillers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('docket_field_grid_id')->unsigned();
            $table->foreign('docket_field_grid_id')->references('id')->on('docket_field_grids');
            $table->string('value');
            $table->integer('index')->default('0');
            $table->integer('root_id')->default('0');
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
        Schema::dropIfExists('docket_grid_prefillers');
    }
}
