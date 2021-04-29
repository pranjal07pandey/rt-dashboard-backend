<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMachineIdToLeaveManagement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('leave_management', function (Blueprint $table) {
        //     $table->dropForeign('leave_management_user_id_foreign');
        // });
        Schema::table('leave_management', function (Blueprint $table) {
            $table->integer('machine_id')->nullable()->unsigned();
        });
        Schema::table('leave_management', function (Blueprint $table) {
            DB::statement('ALTER TABLE `leave_management` MODIFY COLUMN `user_id` integer(11) NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_management', function (Blueprint $table) {
            //
        });
    }
}
