<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePurchaseOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->string('purchase_order_number');
                $table->string('invoice_number')->nullable();
                $table->date('date');
                $table->unsignedBigInteger('supplier_id');
                $table->double('discount_price')->default(0);
                $table->double('subtotal')->default(0);
                $table->double('total')->default(0);
                $table->string('payment_status');
                $table->enum('status', ['draft', 'posted', 'completed'])->default('draft');
                $table->text('address')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict')->onUpdate('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}
