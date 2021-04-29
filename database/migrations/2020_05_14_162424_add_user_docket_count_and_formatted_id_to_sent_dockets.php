<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserDocketCountAndFormattedIdToSentDockets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sent_dockets', function (Blueprint $table) {
            $table->integer('user_docket_count')->default(0);
            $table->string('formatted_id')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sent_dockets', function (Blueprint $table) {
           $table->dropColumn('user_docket_count');
           $table->dropColumn('formatted_id');
        });
    }
}
