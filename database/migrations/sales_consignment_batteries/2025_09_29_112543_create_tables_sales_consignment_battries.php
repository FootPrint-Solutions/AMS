<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablesSalesConsignmentBattries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_consignment_batteries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sales_consignment_id');
            $table->string('sales_invoice_number')->unique();
            $table->string('invoice_number')->nullable();
            $table->date('date');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('distributor_shop_id')->nullable();
            $table->unsignedBigInteger('distributor_shop_technician_id')->nullable();
            $table->decimal('discount', 5, 2)->default(0.00);
            $table->double('discount_price')->default(0);
            $table->double('subtotal')->default(0);
            $table->double('total')->default(0);
            $table->string('payment_status');
            $table->enum('status', ['draft', 'posted', 'completed'])->default('draft');
            $table->text('address')->nullable();
            $table->text('alternative_address')->nullable();
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('midtrans_invoice_number')->nullable();
            $table->string('midtrans_payment_link')->nullable();
            $table->string('source_platform')->default('internal');
            $table->string('source_id')->default('0');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('distributor_shop_id')->references('id')->on('distributor_shops')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('distributor_shop_technician_id', 'fk_sc_batt_dist_shop_tech_id')->references('id')->on('distributor_shop_technicians')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('sales_consignment_id')->references('id')->on('sales_consignments')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_consignment_batteries');
    }
}
