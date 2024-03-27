<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateQuotationNumberInSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropUnique('sales_orders_quotation_number_unique');
            $table->renameColumn('quotation_number', 'sales_order_number');
            $table->unique('sales_orders_number');
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
