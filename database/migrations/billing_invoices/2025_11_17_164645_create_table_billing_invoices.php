<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBillingInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('billing_id');
            $table->unsignedBigInteger('invoice_id');
            $table->string('invoice_type');
            $table->string('invoice_number')->unique();
            $table->double('date');

            $table->decimal('discount', 5, 2);
            $table->double('discount_price');
            $table->double('subtotal');
            $table->double('total');

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
        Schema::dropIfExists('billing_invoices');
    }
}
