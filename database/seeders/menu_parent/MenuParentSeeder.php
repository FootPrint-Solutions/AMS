<?php

namespace Database\Seeders\menu_parent;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing data in menu_parent table.
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('menu_parent')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');


        // Insert menu parent data to menu_parent table.
        DB::table('menu_parent')->insert([
            ['name' => 'Dashboard', 'order' => 1, 'url' => '/', 'hide' => 0, 'icon' => 'fas fa-dashboard', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Master Data', 'order' => 2, 'url' => '#', 'hide' => 0, 'icon' => 'fas fa-book', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Orders', 'order' => 3, 'url' => '#', 'hide' => 0, 'icon' => 'fas fa-receipt', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
