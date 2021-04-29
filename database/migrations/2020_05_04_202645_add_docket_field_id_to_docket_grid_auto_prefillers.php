<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDocketFieldIdToDocketGridAutoPrefillers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docket_grid_auto_prefillers', function (Blueprint $table) {
            $table->integer('docket_field_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docket_grid_auto_prefillers', function (Blueprint $table) {
            $table->dropColumn('docket_field_id');
        });
    }
}
