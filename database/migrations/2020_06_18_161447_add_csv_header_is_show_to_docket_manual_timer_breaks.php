<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCsvHeaderIsShowToDocketManualTimerBreaks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docket_manual_timer_breaks', function (Blueprint $table) {
            $table->string('csv_header')->default(null);
            $table->integer('is_show')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docket_manual_timer_breaks', function (Blueprint $table) {
            $table->dropColumn('csv_header');
            $table->dropColumn('is_show');
        });
    }
}
