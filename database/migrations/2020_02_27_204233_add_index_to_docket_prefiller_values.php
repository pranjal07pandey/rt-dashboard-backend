<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToDocketPrefillerValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docket_prefiller_values', function (Blueprint $table) {
           $table->integer('index')->default(0);
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
        Schema::table('docket_prefiller_values', function (Blueprint $table) {
            $table->dropColumn('index');
            $table->integer('root_id');
        });
    }
}
