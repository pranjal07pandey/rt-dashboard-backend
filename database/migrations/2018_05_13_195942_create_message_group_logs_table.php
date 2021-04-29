<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageGroupLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_group_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type');
            $table->text('message');
            $table->integer('user_id')->unsigned();
            $table->integer('made_by')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('made_by')->references('id')->on('users');
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
        Schema::dropIfExists('message_group_logs');
    }
}
