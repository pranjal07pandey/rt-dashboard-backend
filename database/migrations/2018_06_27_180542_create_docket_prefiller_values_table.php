<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocketPrefillerValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docket_prefiller_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('docket_prefiller_id')->unsigned();
            $table->foreign('docket_prefiller_id')->references('id')->on('docket_prefillers');
            $table->text("label");
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
        Schema::dropIfExists('docket_prefiller_values');
    }
}
