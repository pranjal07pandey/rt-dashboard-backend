<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocketConstantFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docket_constant_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label');
            $table->string('csv_header');
            $table->integer('is_show');
            $table->integer('docket_field_id')->unsigned();
            $table->foreign('docket_field_id')->references('id')->on('docket_fields');
            $table->integer('export_mapping_field_category_id')->unsigned();
            $table->foreign('export_mapping_field_category_id')->references('id')->on('export_mapping_field_categories');
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
        Schema::dropIfExists('docket_constant_fields');
    }
}
