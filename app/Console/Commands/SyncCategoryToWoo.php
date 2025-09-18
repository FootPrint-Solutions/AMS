<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Automattic\WooCommerce\Client;
use App\Models\MasterData\Vehicle\VehicleModel;

class SyncCategoryToWoo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woo:sync-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync categories to WooCommerce';

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
            'https://akikita.id/',
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

            $brandUpdate = [];
            $brandCreate = [];
            $categoryUpdate = [];
            $categoryCreate = [];

            foreach ($vehicles as $vehicle) {
                $brandName = $vehicle->brand->name ?? 'Uncategorized';
                $brandSlug = strtolower(str_replace(' ', '-', $brandName));
                $findBrandSlug = $this->woocommerce->get('products/categories', ['slug' => $brandSlug]);

                if ($findBrandSlug == null || empty($findBrandSlug)) {
                    $brandCreate[] = [
                        'name' => $brandName,
                        'slug' => $brandSlug,
                        'description' => 'Parent Category Brand ' . $brandName,
                        'parent' => 0,
                    ];


                    $this->info("Parent : " . $brandName . ' will be created');
                } else {
                    $brandUpdate[] = [
                        'id' => $findBrandSlug[0]->id,
                        'name' => $brandName
                    ];


                    $this->info("Parent : " . $brandName . ' will be updated');
                }


                $categoryName = $vehicle->name ?? 'Uncategorized';
                $categorySlug = strtolower(str_replace(' ', '-', $categoryName));
                $findCategorySlug = $this->woocommerce->get('products/categories', ['slug' => $categorySlug]);

                if ($findCategorySlug == null || empty($findCategorySlug)) {
                    $categoryCreate[] = [
                        'name' => $categoryName,
                        'slug' => $categorySlug,
                        'description' => 'Category ' . $categoryName,
                        'parent' => $findBrandSlug[0]->id,
                    ];


                    $this->info("Category : " . $categoryName . ' will be created');
                } else {
                    $categoryUpdate[] = [
                        'id' => $findCategorySlug[0]->id,
                        'name' => $categoryName
                    ];


                    $this->info("Category : " . $categoryName . ' will be updated');
                }
            }


            if (!empty($brandUpdate)) {
                $this->woocommerce->post('products/categories/batch', ['update' => $brandUpdate]);
            }
            if (!empty($categoryUpdate)) {
                $this->woocommerce->post('products/categories/batch', ['update' => $categoryUpdate]);
            }


            if (!empty($brandCreate)) {
                $this->woocommerce->post('products/categories/batch', ['create' => $brandCreate]);
            }
            if (!empty($categoryCreate)) {
                $this->woocommerce->post('products/categories/batch', ['create' => $categoryCreate]);
            }


            $this->info('Processed ' . count($vehicles) . ' vehicles');


            $offset += $limit;
        }


        $this->info('Success to sync categories to WooCommerce');
    }
}
