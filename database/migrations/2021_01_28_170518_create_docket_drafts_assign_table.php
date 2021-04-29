<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocketDraftsAssignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docket_drafts_assign', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assign_docket_user_id')->unsigned();
            $table->foreign('assign_docket_user_id')->references('id')->on('assign_docket_user');
            $table->integer('docket_id')->unsigned();
            $table->foreign('docket_id')->references('id')->on('dockets');
            $table->integer('docket_draft_id')->unsigned();
            $table->foreign('docket_draft_id')->references('id')->on('docket_drafts');
            $table->integer('user_id')->nullable()->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('machine_id')->nullable()->unsigned();
            $table->foreign('machine_id')->references('id')->on('machines');
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
        Schema::dropIfExists('docket_drafts_assign');
    }
}
