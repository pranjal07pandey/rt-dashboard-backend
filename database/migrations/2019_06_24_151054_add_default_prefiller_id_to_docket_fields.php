<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultPrefillerIdToDocketFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docket_fields', function (Blueprint $table) {
            $table->text('default_prefiller_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docket_fields', function (Blueprint $table) {
            $table->dropColumn('default_prefiller_id');
        });
    }
}