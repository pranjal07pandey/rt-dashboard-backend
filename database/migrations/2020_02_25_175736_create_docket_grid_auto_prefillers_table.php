<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocketGridAutoPrefillersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docket_grid_auto_prefillers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('grid_field_id');
            $table->integer('index');
            $table->integer('link_grid_field_id');
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
        Schema::dropIfExists('docket_grid_auto_prefillers');
    }
}
