<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnForVehicle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add new column for vehicle, vehicle_years_id, vehicle_fuels_id, vehicle_transmissions_id
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_years_id')->nullable()->after('status');
            $table->unsignedBigInteger('vehicle_fuels_id')->nullable()->after('vehicle_years_id');
            $table->unsignedBigInteger('vehicle_transmissions_id')->nullable()->after('vehicle_fuels_id');
            $table->foreign('vehicle_years_id')->references('id')->on('vehicle_years');
            $table->foreign('vehicle_fuels_id')->references('id')->on('vehicle_fuels');
            $table->foreign('vehicle_transmissions_id')->references('id')->on('vehicle_transmissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop new column for vehicle, vehicle_years_id, vehicle
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['vehicle_years_id']);
            $table->dropForeign(['vehicle_fuels_id']);
            $table->dropForeign(['vehicle_transmissions_id']);
            $table->dropColumn('vehicle_years_id');
            $table->dropColumn('vehicle_fuels_id');
            $table->dropColumn('vehicle_transmissions_id');
        });
    }
}
