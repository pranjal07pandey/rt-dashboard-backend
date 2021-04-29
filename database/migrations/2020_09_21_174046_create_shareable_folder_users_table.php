<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareableFolderUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shareable_folder_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shareable_folder_id')->unsigned();
            $table->string('email');
            $table->string('password');
            $table->string('token')->nullable();
            $table->foreign('shareable_folder_id')->references('id')->on('shareable_folders');
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
        Schema::dropIfExists('shareable_folder_users');
    }
}
