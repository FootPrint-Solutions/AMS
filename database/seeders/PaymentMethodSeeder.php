<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
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
        DB::table('payment_methods')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Insert menu data to menu table.
        DB::table('payment_methods')->insert([
            ['id' => 1, 'name' => 'Midtrans', 'note' => 'The default payment method cannot be deleted.', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
