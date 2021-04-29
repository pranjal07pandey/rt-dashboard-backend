<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDocketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dockets', function (Blueprint $table) {
            $table->string('docket_frequency_slug')->nullable();
            $table->date('remainder_date')->nullable();
            $table->longText('custom_text')->nullable();
        });
    }
}
