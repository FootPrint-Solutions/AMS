<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxAndDiscountPriceToSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->double('tax_price')->default(0)->after('tax');
            $table->double('discount_price')->default(0)->after('discount');
            $table->double('extra_discount_price')->default(0)->after('extra_discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            //
        });
    }
}
