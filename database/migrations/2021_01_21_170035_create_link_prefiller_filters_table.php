<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkPrefillerFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_prefiller_filters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('docket_field_id')->unsigned();
            $table->foreign('docket_field_id')->references('id')->on('docket_fields');
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
        Schema::dropIfExists('link_prefiller_filters');
    }
}
