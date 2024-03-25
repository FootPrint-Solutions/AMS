<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSalesOrdersView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW sales_orders_view AS
                        SELECT sales_orders.*, customers.name AS customer_name, shops.name AS shop_name, distributors.id AS distributor_id, distributors.name AS distributor_name, technicians.name AS technician_name
                        FROM sales_orders
                            LEFT JOIN customers ON sales_orders.customer_id = customers.id
                            LEFT JOIN distributor_shops AS shops ON sales_orders.distributor_shop_id = shops.id
                            LEFT JOIN distributors ON shops.distributor_id = distributors.id
                            LEFT JOIN distributor_shop_technicians AS technicians ON sales_orders.distributor_shop_technician_id = technicians.id");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW sales_orders_view");
    }
}
