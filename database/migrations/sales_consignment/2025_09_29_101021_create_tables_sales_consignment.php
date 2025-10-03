<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablesSalesConsignment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_consignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sales_consignment_number', 255)->unique();
            $table->date('date');
            $table->decimal('discount', 5, 2)->default(0.00);
            $table->double('discount_price')->default(0);
            $table->double('subtotal')->default(0);
            $table->double('total_expenses')->default(0);
            $table->double('total')->default(0);
            $table->string('payment_status', 255);
            $table->enum('status', ['draft', 'posted', 'completed'])->default('draft');
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
        Schema::dropIfExists('sales_consignments');
    }
}
