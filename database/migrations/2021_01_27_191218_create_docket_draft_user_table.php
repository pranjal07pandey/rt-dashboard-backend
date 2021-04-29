<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocketDraftUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_docket_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('assigned_by')->nullable()->unsigned();
            $table->foreign('assigned_by')->references('id')->on('users');
            $table->string('from_date');
            $table->string('to_date');
            $table->string('bgcolor')->default('#022e55');
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
        Schema::dropIfExists('docket_draft_user');
    }
}
