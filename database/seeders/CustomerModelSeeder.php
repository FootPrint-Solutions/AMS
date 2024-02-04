<?php

namespace Database\Seeders;

use App\Models\MasterData\CustomerModel;
use Illuminate\Database\Seeder;

class CustomerModelSeeder extends Seeder
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
