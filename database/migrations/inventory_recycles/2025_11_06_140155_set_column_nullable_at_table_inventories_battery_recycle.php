<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetColumnNullableAtTableInventoriesBatteryRecycle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_recycles', function (Blueprint $table) {
            $table->unsignedBigInteger('battery_id')->nullable()->change();
        });

        Schema::table('inventory_recycle_details', function (Blueprint $table) {
            $table->unsignedBigInteger('battery_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_recycles', function (Blueprint $table) {
            $table->unsignedBigInteger('battery_id')->nullable(false)->change();
        });

        Schema::table('inventory_recycle_details', function (Blueprint $table) {
            $table->unsignedBigInteger('battery_id')->nullable(false)->change();
        });
    }
}
