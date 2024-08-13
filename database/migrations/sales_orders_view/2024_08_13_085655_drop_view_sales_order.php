<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropViewSalesOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // drop view sales_order
        DB::statement("DROP VIEW sales_orders");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // create view sales_order
        DB::statement("CREATE VIEW sales_order AS
                        SELECT sales_orders.*, customers.name AS customer_name, shops.name AS shop_name, distributors.id AS distributor_id, distributors.name AS distributor_name, technicians.name AS technician_name
                        FROM sales_orders
                            LEFT JOIN customers ON sales_orders.customer_id = customers.id
                            LEFT JOIN distributor_shops AS shops ON sales_orders.distributor_shop_id = shops.id
                            LEFT JOIN distributors ON shops.distributor_id = distributors.id
                            LEFT JOIN distributor_shop_technicians AS technicians ON sales_orders.distributor_shop_technician_id = technicians.id");
    }
}
