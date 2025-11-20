<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBilling extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->string('billing_number')->unique();
            $table->unsignedBigInteger('vendor_id');
            $table->string('vendor_type');
            $table->unsignedBigInteger('ship_to_id');
            $table->string('ship_to_type');
            $table->date('date');

            $table->decimal('discount', 5, 2);
            $table->double('discount_price');
            $table->double('subtotal');
            $table->double('total');

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
        Schema::dropIfExists('billings');
    }
}
