<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateQuotationsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW quotations_view AS
                        SELECT quotations.*, customers.name AS customer_name, shops.name AS shop_name, technicians.name AS technician_name
                        FROM quotations
                            LEFT JOIN customers ON quotations.customer_id = customers.id
                            LEFT JOIN distributor_shops AS shops ON quotations.distributor_shop_id = shops.id
                            LEFT JOIN distributor_shop_technicians AS technicians ON quotations.distributor_shop_technician_id = technicians.id");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW quotations_view");
    }
}
