<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatteryTradeInTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battery_trade_in', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_size_category');
            $table->double('price');
            $table->timestamps();

            /*
            $table->foreign('id_size_category')
                ->references('id')
                ->on('battery_size_category');
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
        Schema::dropIfExists('battery_trade_in');
    }
}
