<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddModifyIconImageSizeToYesNoFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yes_no_fields', function (Blueprint $table) {
            DB::statement('ALTER TABLE yes_no_fields MODIFY icon_image text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yes_no_fields', function (Blueprint $table) {
            //
        });
    }
}
