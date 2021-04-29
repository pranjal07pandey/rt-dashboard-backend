<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageGroupMsgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_group_msgs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('message_group_id')->unsigned();
            $table->integer('sender_user_id')->unsigned();
            $table->string('title');
            $table->text('message');

            $table->foreign('message_group_id')->references('id')->on('message_groups');
            $table->foreign('sender_user_id')->references('id')->on('users');
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
        Schema::dropIfExists('message_group_msgs');
    }
}
