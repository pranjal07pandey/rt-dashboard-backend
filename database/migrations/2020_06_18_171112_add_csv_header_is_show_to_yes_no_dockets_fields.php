<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCsvHeaderIsShowToYesNoDocketsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yes_no_dockets_fields', function (Blueprint $table) {
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
        Schema::table('yes_no_dockets_fields', function (Blueprint $table) {
            $table->dropColumn('csv_header');
            $table->dropColumn('is_show');
        });
    }
}
