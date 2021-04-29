<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastEditedValueIdToSentDocketsValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sent_dockets_values', function (Blueprint $table) {
            $table->integer('last_edited_value_id')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sent_dockets_values', function (Blueprint $table) {
            $table->dropColumn('last_edited_value_id');
        });
    }
}
