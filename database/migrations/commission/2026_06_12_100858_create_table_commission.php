<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCommission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission', function (Blueprint $table) {
            $table->id();
            $table->string('commission_number')->unique();
            $table->date('date');
            $table->double('total');
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });

        // commission_items table
        Schema::create('commission_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('commission_id');
            $table->unsignedBigInteger('distributor_shop_id');
            $table->unsignedBigInteger('sales_order_id');
            $table->unsignedBigInteger('battery_id');
            $table->string('commission_type');
            $table->double('commission_amount');
            $table->unsignedBigInteger('credit_account_id');
            $table->unsignedBigInteger('debit_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commission');
        Schema::dropIfExists('commission_items');
    }
}
