<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Automattic\WooCommerce\Client;
use App\Models\MasterData\Vehicle\VehicleModel;

class SyncTermCategoryToWoo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woo:sync-term-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync term categories to WooCommerce';

    private $woocommerce;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->woocommerce = new Client(
            'https://akikita.web.id/',
            'ck_7034e6f1e7a7d3b705df60c37bb003c6a1ca6f9b',
            'cs_b7973fc68cdd299d2ad6647989872e517d88cab0',
            [
                'version' => 'wc/v3',
            ]
        );
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $limit = 10;
        $offset = 0;


        $total = VehicleModel::count();

        while ($offset < $total) {

            $vehicles = VehicleModel::with('brand')->orderBy('name', 'asc')->limit($limit)->offset($offset)->get();

            if ($vehicles->isEmpty()) {
                break;
            }

            $categoryCreate = [];

            foreach ($vehicles as $vehicle) {
                $brandName = $vehicle->brand->name ?? 'Uncategorized';
                $categoryName = $vehicle->name ?? 'Uncategorized';
                $categorySlug = strtolower(str_replace(' ', '-', $categoryName));

                $data = [
                    'name' => $categoryName,
                    'slug' => $categorySlug,
                ];

                $categoryCreate[] = $data;

                $this->info("Category : " . $categoryName . ' will be created');
            }

            if (!empty($categoryCreate)) {
                $this->woocommerce->post('products/attributes/7/terms/batch', ['create' => $categoryCreate]);
            }


            $this->info('Processed ' . count($categoryCreate) . ' vehicles');

            $offset += $limit;
        }
    }
}
