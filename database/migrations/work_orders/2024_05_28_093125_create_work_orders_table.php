<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_number')->unique();
            $table->date('date');
            $table->unsignedBigInteger('sales_order_id');
            $table->unsignedBigInteger('customer_id');
            $table->decimal('tax', 5, 2)->default(0);
            $table->double('tax_price')->default(0);
            $table->decimal('discount', 5, 2)->default(0);
            $table->double('discount_price')->default(0);
            $table->decimal('extra_discount', 5, 2)->default(0);
            $table->double('extra_discount_price')->default(0);
            $table->double('total')->default(0);
            $table->text('address');
            $table->text('latitude');
            $table->text('longitude');
            $table->timestamps();
            $table->softDeletes();

            // Set foreign key.
            $table->foreign('sales_order_id')
                ->references('id')
                ->on('sales_orders');
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_orders');
    }
}
