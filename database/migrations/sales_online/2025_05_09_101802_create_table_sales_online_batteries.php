<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSalesOnlineBatteries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_online_batteries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_online_id');
            $table->unsignedBigInteger('battery_id');
            $table->string('name');
            $table->double('price');
            $table->string('image')->nullable();
            $table->double('quantity');
            $table->double('total_price');
            $table->timestamps();

            $table->foreign('sales_online_id')->references('id')->on('sales_online')->onDelete('cascade');
            $table->foreign('battery_id')->references('id')->on('batteries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_online_batteries');
    }
}
