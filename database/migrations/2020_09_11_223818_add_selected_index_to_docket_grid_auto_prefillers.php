<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSelectedIndexToDocketGridAutoPrefillers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docket_grid_auto_prefillers', function (Blueprint $table) {
            $table->string('selected_index')->default(null);
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
            $table->dropColumn('selected_index');
        });
    }
}
