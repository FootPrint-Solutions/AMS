<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSalesOnline extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_online', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('province');
            $table->string('city');
            $table->string('district');
            $table->string('sub_district');
            $table->string('postal_code')->nullable();
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->date('delivery_date')->nullable();
            $table->text('additional_info')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_online');
    }
}
