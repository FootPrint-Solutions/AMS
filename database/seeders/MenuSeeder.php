<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing data in menu table.
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('menus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Insert menu data to menu table.
        DB::table('menus')->insert([
            ['name' => 'Company', 'parent_id' => 2, 'order' => 1, 'url' => '/company', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Customer', 'parent_id' => 2, 'order' => 2, 'url' => '/customer', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vehicle', 'parent_id' => 2, 'order' => 3, 'url' => '/vehicle', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Battery', 'parent_id' => 2, 'order' => 4, 'url' => '/battery', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Distributor', 'parent_id' => 2, 'order' => 5, 'url' => '/distributor', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Quick Quotation', 'parent_id' => 3, 'order' => 1, 'url' => '/quotation', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
