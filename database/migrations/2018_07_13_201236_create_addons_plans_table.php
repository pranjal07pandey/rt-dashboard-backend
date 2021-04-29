<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddonsPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addons_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('addon_id')->unsigned();
            $table->foreign('addon_id')->references('id')->on('addons');
            $table->string('plan_id');
            $table->integer('max_user');
            $table->string('amount');
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
        Schema::dropIfExists('addons_plans');
    }
}
