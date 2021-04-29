<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSynXeroContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syn_xero_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('contact_name');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('xero_contact_id');
            $table->string('company_id');
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
        Schema::dropIfExists('syn_xero_contacts');
    }
}
