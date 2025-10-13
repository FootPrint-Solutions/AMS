<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnVendorAtTableSalesConsignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_consignments', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('sales_consignment_number');
            $table->string('vendor_name')->nullable()->after('vendor_id');
            $table->unsignedBigInteger('ship_to_id')->nullable()->after('vendor_name');
            $table->string('ship_to_name')->nullable()->after('ship_to_id');
            $table->dropColumn('to');

            $table->foreign('vendor_id')->references('id')->on('distributors')->onDelete('set null');
            $table->foreign('ship_to_id')->references('id')->on('distributor_shops')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_consignments', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['ship_to_id']);
            $table->dropColumn(['vendor_id', 'vendor_name', 'ship_to_id', 'ship_to_name']);
            $table->string('to')->nullable()->after('sales_consignment_number');
        });
    }
}
