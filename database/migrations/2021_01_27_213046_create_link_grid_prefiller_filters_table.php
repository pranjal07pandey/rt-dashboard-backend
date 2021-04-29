<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkGridPrefillerFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_grid_prefiller_filters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('docket_field_grid_id')->unsigned();
            $table->foreign('docket_field_grid_id')->references('id')->on('docket_field_grids');
            $table->text('link_prefiller_filter_label');
            $table->text('link_prefiller_filter_value');
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
        Schema::dropIfExists('link_grid_prefiller_filters');
    }
}
