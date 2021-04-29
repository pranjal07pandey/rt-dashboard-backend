<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSelectedIndexAndPrefillerEcowiseIdToDocketFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docket_fields', function (Blueprint $table) {
            $table->integer('echowise_id');
            $table->string('selected_index')->nullable();
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
           $table->dropColumn('echowise_id');
            $table->dropColumn('selected_index');
        });
    }
}
