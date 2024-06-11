<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveTaxAndTaxPriceWorkOrdersMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // remove tax and tax_price from work_orders_master
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('tax');
            $table->dropColumn('tax_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // add tax and tax_price to work_orders_master
        Schema::table('work_orders', function (Blueprint $table) {
            $table->decimal('tax', 5, 2)->default(0);
            $table->double('tax_price')->default(0);
        });
    }
}
