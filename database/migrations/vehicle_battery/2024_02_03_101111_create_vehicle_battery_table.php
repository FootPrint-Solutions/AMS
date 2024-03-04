<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleBatteryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_battery', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('battery_id');
            $table->tinyInteger('type')->default('0')->comment('0: secondary, 1: primary');
            $table->timestamps();

            // Set foreign key.
            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles');
            $table->foreign('battery_id')
                ->references('id')
                ->on('batteries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_battery');
    }
}
