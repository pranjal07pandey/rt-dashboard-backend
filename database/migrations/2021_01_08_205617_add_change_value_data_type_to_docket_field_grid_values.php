<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChangeValueDataTypeToDocketFieldGridValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docket_field_grid_values', function (Blueprint $table) {
            DB::statement('ALTER TABLE docket_field_grid_values MODIFY COLUMN value LONGTEXT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docket_field_grid_values', function (Blueprint $table) {
            DB::statement('ALTER TABLE docket_field_grid_values MODIFY COLUMN value VARCHAR(255)');
        });
    }
}
