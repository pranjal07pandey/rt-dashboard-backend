<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyLogoAndTitleToSentDockets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sent_dockets', function (Blueprint $table) {
            $table->string('company_logo')->nullable();
            $table->string('template_title')->nullable();
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
            $table->dropColumn('company_logo');
            $table->dropColumn('template_title');
        });
    }
}
