<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddMakeDocketIdNullableToAssignDocketUserConnection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assign_docket_user_connection', function (Blueprint $table) {
            $table->dropForeign(['docket_id']);
        });
        Schema::table('assign_docket_user_connection', function (Blueprint $table) {
            DB::statement('ALTER TABLE `assign_docket_user_connection` MODIFY COLUMN `docket_id` integer(11) NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assign_docket_user_connection', function (Blueprint $table) {
            //
        });
    }
}
