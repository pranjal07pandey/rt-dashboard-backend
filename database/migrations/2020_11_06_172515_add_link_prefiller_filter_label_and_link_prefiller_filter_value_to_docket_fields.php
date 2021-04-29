<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLinkPrefillerFilterLabelAndLinkPrefillerFilterValueToDocketFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docket_fields', function (Blueprint $table) {
             $table->string('link_prefiller_filter_label');
             $table->string('link_prefiller_filter_value');
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
            $table->dropColumn();
        });
    }
}
