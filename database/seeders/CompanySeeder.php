<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
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
        DB::table('company')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');


        // Insert menu parent data to menu_parent table.
        DB::table('company')->insert([
            ['name' => '', 'address' => '', 'contact' => '', 'email' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
