<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYesNoDocketsFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yes_no_dockets_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('yes_no_field_id')->unsigned();
            $table->foreign('yes_no_field_id')->references('id')->on('yes_no_fields');
            $table->integer('docket_field_category_id')->unsigned();
            $table->foreign('docket_field_category_id')->references('id')->on('docket_filed_categories');
            $table->integer('order');
            $table->integer('required');
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
        Schema::dropIfExists('yes_no_dockets_fields');
    }
}
