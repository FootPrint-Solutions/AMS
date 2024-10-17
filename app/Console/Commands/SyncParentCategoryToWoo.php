<?php

namespace App\Console\Commands;

use App\Models\MasterData\Vehicle\VehicleBrandModel;
use Illuminate\Console\Command;
use Automattic\WooCommerce\Client;

class SyncParentCategoryToWoo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woo:sync-parent-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync parent categories to WooCommerce';

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
        $total = VehicleBrandModel::count();

        while ($offset < $total) {

            $brands = VehicleBrandModel::orderBy('name', 'asc')->limit($limit)->offset($offset)->get();

            if ($brands->isEmpty()) {
                break;
            }

            $brandUpdate = [];
            $brandCreate = [];


            foreach ($brands as $brand) {
                $brandName = $brand->brand->name ?? 'Uncategorized';
                $brandSlug = strtolower(str_replace(' ', '-', $brandName));
                $findBrandSlug = $this->woocommerce->get('products/categories', ['slug' => $brandSlug]);

                if ($findBrandSlug == null || empty($findBrandSlug)) {
                    $data = [
                        'name' => $brandName,
                        'slug' => $brandSlug,
                        'description' => 'Parent Category Brand ' . $brandName,
                        'parent' => 0,
                    ];

                    $brandCreate[] = $data;

                    $this->info("Parent : " . $brandName . ' will be created');
                } else {
                    $data = [
                        'name' => $brandName,
                        'slug' => $brandSlug,
                        'description' => 'Parent Category Brand ' . $brandName,
                        'parent' => 0,
                    ];

                    $brandUpdate[] = $data;

                    $this->info("Parent : " . $brandName . ' will be updated');
                }
            }

            if (!empty($brandUpdate)) {
                $this->woocommerce->post('products/categories/batch', ['update' => $brandUpdate]);
            }

            if (!empty($brandCreate)) {
                $this->woocommerce->post('products/categories/batch', ['create' => $brandCreate]);
            }

            $this->info('Processed ' . count($brands) . ' vehicles');

            $offset += $limit;
        }
    }
}
