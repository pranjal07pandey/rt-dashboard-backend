<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInviceDocketTimerClientToCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('can_invoice');
            $table->boolean('can_docket');
            $table->boolean('can_timer');
            $table->boolean('docket_client');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('can_invoice');
            $table->dropColumn('can_docket');
            $table->dropColumn('can_timer');
            $table->dropColumn('docket_client');
        });
    }
}
