<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSubSeeder extends Seeder
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
        DB::table('menu_subs')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Insert menu data to menu table.
        DB::table('menu_subs')->insert([
            // VEHICLE
            ['name' => 'Brand', 'menu_id' => 3, 'order' => 1, 'url' => '/vehicle/brand', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
            // BATTERY
            ['name' => 'Brand', 'menu_id' => 4, 'order' => 1, 'url' => '/battery/brand', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Subbrand Category', 'menu_id' => 4, 'order' => 2, 'url' => '/battery/subbrand', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Usage Type', 'menu_id' => 4, 'order' => 3, 'url' => '/battery/usage', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Technology', 'menu_id' => 4, 'order' => 4, 'url' => '/battery/technology', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Size Category', 'menu_id' => 4, 'order' => 5, 'url' => '/battery/size', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
            // PARTNER
            ['name' => 'Shop', 'menu_id' => 5, 'order' => 2, 'url' => '/distributor/shop', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Technician', 'menu_id' => 5, 'order' => 3, 'url' => '/distributor/technician', 'hide' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
