<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('distributor_shop_id');
            $table->unsignedBigInteger('distributor_shop_technician_id');
            $table->decimal('tax', 5, 2)->default(0);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('extra_discount', 5, 2)->default(0);
            $table->double('total')->default(0);
            $table->string('midtrans_invoice_number')->nullable();
            $table->string('midtrans_payment_link')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Set foreign key.
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers');
            $table->foreign('distributor_shop_id')
                ->references('id')
                ->on('distributor_shops');
            $table->foreign('distributor_shop_technician_id')
                ->references('id')
                ->on('distributor_shop_technicians');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}
