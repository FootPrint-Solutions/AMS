<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorShopBatteryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_shop_battery', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_shop_id');
            $table->unsignedBigInteger('battery_id');
            $table->double('price')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();

            // Set foreign key references.
            $table->foreign('distributor_shop_id')
                ->references('id')
                ->on('distributor_shops');
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
        Schema::dropIfExists('distributor_shop_battery');
    }
}
