<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationBatteryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_battery', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('battery_id');
            $table->string('battery_name');
            $table->double('battery_price');
            $table->double('quantity');
            $table->timestamps();

            // Set foreign key.
            $table->foreign('quotation_id')
                ->references('id')
                ->on('quotations');
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
        Schema::dropIfExists('quotation_battery');
    }
}
