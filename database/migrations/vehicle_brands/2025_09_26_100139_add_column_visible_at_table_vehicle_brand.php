<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnVisibleAtTableVehicleBrand extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add column visible to table vehicle_brands
        Schema::table('vehicle_brands', function (Blueprint $table) {
            $table->boolean('visible')->default(true)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove column visible from table vehicle_brands
        Schema::table('vehicle_brands', function (Blueprint $table) {
            $table->dropColumn('visible');
        });
    }
}
