<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRootIdToDocketFiledPreFillers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docket_filed_pre_fillers', function (Blueprint $table) {
            $table->integer('root_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docket_filed_pre_fillers', function (Blueprint $table) {
            $table->dropColumn('root_id');
        });
    }
}
