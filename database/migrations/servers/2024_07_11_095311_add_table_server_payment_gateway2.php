<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableServerPaymentGateway2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add table server_payment_gateway to store payment gateway data like payment gateway name, server key, client key, id_merchant, and status active
        Schema::create('server_payment_gateway', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('server_key');
            $table->string('client_key');
            $table->string('id_merchant');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop table server_payment_gateway
        Schema::dropIfExists('server_payment_gateway');
    }
}
