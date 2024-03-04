<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
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
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Insert menu data to menu table.
        DB::table('roles')->insert([
            ['name' => 'Default', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
