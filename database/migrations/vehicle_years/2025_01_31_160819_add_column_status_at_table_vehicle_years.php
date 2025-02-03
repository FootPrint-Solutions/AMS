<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnStatusAtTableVehicleYears extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the column.
        Schema::table('vehicle_years', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->after('end_year')->comment('0: Inactive, 1: Active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the column.
        Schema::table('vehicle_years', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
