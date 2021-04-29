<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultDocketUnitRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_docket_unit_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('default_docket_field_id')->unsigned();
            $table->foreign('default_docket_field_id')->references('id')->on('default_docket_fields');
            $table->integer('type');
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
        Schema::dropIfExists('default_docket_unit_rates');
    }
}
