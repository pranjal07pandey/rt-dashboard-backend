<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsEmailSubjectToDocketFieldGrids extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docket_field_grids', function (Blueprint $table) {
           $table->integer('is_emailed_subject');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docket_field_grids', function (Blueprint $table) {
            $table->dropColumn('is_emailed_subject');
        });
    }
}
