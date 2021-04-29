<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssignTypeAndDateRangeToAssignedDockets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assigned_dockets', function (Blueprint $table) {
            $table->integer('assign_type')->comment('Regular assign =0, date range = 1');
            $table->string('date_range')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assigned_dockets', function (Blueprint $table) {
            $table->dropColumn();
        });
    }
}
