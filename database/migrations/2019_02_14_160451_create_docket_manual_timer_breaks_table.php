<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocketManualTimerBreaksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docket_manual_timer_breaks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('docket_field_id')->unsigned();
            $table->foreign('docket_field_id')->references('id')->on('docket_fields');
            $table->string('label');
            $table->string('explanation');
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
        Schema::dropIfExists('docket_manual_timer_breaks');
    }
}
