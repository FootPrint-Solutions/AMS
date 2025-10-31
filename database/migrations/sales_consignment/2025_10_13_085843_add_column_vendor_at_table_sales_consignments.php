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
            if (!Schema::hasColumn('sales_consignments', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('sales_consignment_number');
            }

            if (!Schema::hasColumn('sales_consignments', 'vendor_name')) {
                $table->string('vendor_name')->nullable()->after('vendor_id');
            }

            if (!Schema::hasColumn('sales_consignments', 'ship_to_id')) {
                $table->unsignedBigInteger('ship_to_id')->nullable()->after('vendor_name');
            }

            if (!Schema::hasColumn('sales_consignments', 'ship_to_name')) {
                $table->string('ship_to_name')->nullable()->after('ship_to_id');
            }

            // $table->dropColumn('to');

            // Drop foreign keys if they exist to avoid duplicate constraint errors
            if (Schema::hasColumn('sales_consignments', 'vendor_id')) {
                try {
                    $table->dropForeign(['vendor_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, ignore
                }
            }
            if (Schema::hasColumn('sales_consignments', 'ship_to_id')) {
                try {
                    $table->dropForeign(['ship_to_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, ignore
                }
            }

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
