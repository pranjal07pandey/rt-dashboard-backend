<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimesheetDocketAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timesheet_docket_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('timesheet_docket_detail_id')->unsigned();
            $table->foreign('timesheet_docket_detail_id')->references('id')->on('timesheet_docket_details');
            $table->integer('sent_docket_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timesheet_docket_attachments');
    }
}
