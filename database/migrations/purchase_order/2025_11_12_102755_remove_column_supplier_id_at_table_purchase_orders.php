<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnSupplierIdAtTablePurchaseOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // drop foreign key constraint first to allow dropping the column
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');

            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->string('vendor_type')->nullable()->after('vendor_id');

            $table->dropColumn('ship_to');
            $table->unsignedBigInteger('ship_to_id')->nullable()->after('vendor_type');
            $table->string('ship_to_type')->nullable()->after('ship_to_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['vendor_id', 'vendor_type', 'ship_to_id', 'ship_to_type']);
            $table->unsignedBigInteger('supplier_id')->nullable()->after('id');
            $table->string('ship_to')->nullable()->after('supplier_id');

            // re-add foreign key constraint
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }
}
