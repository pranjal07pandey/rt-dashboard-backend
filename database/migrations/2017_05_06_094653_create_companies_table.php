<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name');
            $table->string('address');
            $table->string('contactNumber');
            $table->string('logo');
            $table->string('abn');
            $table->string('stripe_user')->nullable();
            $table->integer('max_user');
            $table->timestamp('renew_date');
            $table->timestamp('expiry_date')->nullable();
            $table->boolean('trial_period')->comment('0: new user; 1 : in trial; 2 : in subscription; 3: trial expire; 4: apple subscription; 5 : canceled');
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
        Schema::dropIfExists('companies');
    }
}
