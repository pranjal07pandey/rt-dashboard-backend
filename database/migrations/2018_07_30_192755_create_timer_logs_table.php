<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimerLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timer_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('timer_id')->unsigned();
            $table->foreign('timer_id')->references('id')->on('timers');
            $table->string('location');
            $table->decimal('longitude', 9, 6);
            $table->decimal('latitude', 9, 6);
            $table->dateTime('time_started');
            $table->dateTime('time_finished')->nullable();
            $table->text('reason');
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
        Schema::dropIfExists('timer_logs');
    }
}
