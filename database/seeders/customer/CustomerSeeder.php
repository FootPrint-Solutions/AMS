<?php

namespace Database\Seeders\customer;

use Illuminate\Database\Seeder;

// MODELS
use App\Models\MasterData\Customer\CustomerModel;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerModel::factory()
            ->count(10)
            ->create();
    }
}
