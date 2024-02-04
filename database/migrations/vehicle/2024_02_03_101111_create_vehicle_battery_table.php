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
            $table->unsignedBigInteger('id_vehicle');
            $table->unsignedBigInteger('id_battery');
            $table->timestamps();

            /*
            $table->foreign('id_vehicle')
                ->references('id')
                ->on('vehicle');
            $table->foreign('id_battery')
                ->references('id')
                ->on('battery');
            */
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
