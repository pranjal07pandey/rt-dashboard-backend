<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyXerosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_xeros', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('xero_user_id');
            $table->string('xero_email');
            $table->string('xero_user_first_name');
            $table->string('xero_user_last_name');
            $table->string('xero_organization_id');
            $table->string('xero_organization_name');
            $table->string('xero_organination_address');
            $table->string('xero_organization_contact');
            $table->string('organization_line_of_business');
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
        Schema::dropIfExists('company_xeros');
    }
}
