<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('id_customer');
            $table->unsignedBigInteger('id_battery');
            $table->string('battery_production_code');
            $table->integer('battery_quantity');
            $table->boolean('battery_trade_in');
            $table->boolean('installation');
            $table->unsignedBigInteger('id_mechanic');
            $table->double('cost_delivery');
            $table->double('price_total');
            $table->boolean('status');
            $table->timestamps();

            /*
            $table->foreign('id_customer')
                ->references('id')
                ->on('customer');
            $table->foreign('id_battery')
                ->references('id')
                ->on('battery');
            $table->foreign('id_mechanic')
                ->references('id')
                ->on('mechanic');
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
        Schema::dropIfExists('order');
    }
}
