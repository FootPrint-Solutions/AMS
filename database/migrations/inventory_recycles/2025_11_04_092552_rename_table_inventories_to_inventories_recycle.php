<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTableInventoriesToInventoriesRecycle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::rename('inventories', 'inventory_recycles');
            Schema::rename('inventory_details', 'inventory_recycle_details');
        } catch (\Exception $e) {
            $this->down();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            Schema::rename('inventory_recycles', 'inventories');
            Schema::rename('inventory_recycle_details', 'inventory_details');
        } catch (\Exception $e) {
        }
    }
}
