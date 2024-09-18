<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Automattic\WooCommerce\Client;
use App\Models\MasterData\Battery\BatteryModel;
use Illuminate\Support\Facades\Log;

class SyncProductToWoo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woo:sync-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products to WooCommerce';

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
            'https://wp.raden.social/',
            'ck_e69e713763c055f1a63f9057dfae0bb595775815',
            'cs_1e35df9199bb4cd6e9e1774bf11e0831f089763e',
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
        $limit = 2;
        $offset = 0;
        $total = BatteryModel::count();

        while ($offset < $total) {

            $batteries = BatteryModel::orderBy('name', 'asc')->with('VehicleBattery')->limit($limit)->offset($offset)->get();

            dd($batteries);

            $productUpdate = [];
            $productCreate = [];
            $categoryCreate = [];
            $categoryUpdate = [];

            foreach ($batteries as $battery) {
                $categori = $battery->VehicleBattery->vehicle->name ?? 'Uncategorized';
                $categorySlug = strtolower(str_replace(' ', '-', $categori));
                $category = $this->findCategoryWooBySlug($categorySlug);
                $price = (string) $battery->batteryPricesBelong->price_retail;

                // Jika kategori tidak ditemukan, lewati baterai ini dan lanjut ke iterasi berikutnya
                if ($category == null || count((array) $category) == 0) {
                    continue;
                }

                // Jika kategori ditemukan, tambahkan atau perbarui data kategori
                $categoryUpdate[] = [
                    'id' => $category[0]->id,
                    'name' => $categori,
                    'slug' => $categorySlug,
                ];

                $this->info('Processing battery: ' . $battery->name);

                // Temukan produk berdasarkan slug dari nama battery
                $product = $this->findProductWooByName($battery->name);
                // file:///C://laragon//www//ams//storage//app//public//image//battery//EFJ05JLyTQA8JCpP4jGZp1Fqq5abwteVGwCkkEx1.png
                $battery_image_url  = asset('storage/image/battery/' . $battery->image);

                // Jika produk tidak ditemukan, tambahkan produk baru
                if ($product == null || count((array) $product) == 0) {
                    $productCreate[] = [
                        'name' => $battery->name,
                        'slug' => strtolower(str_replace(' ', '-', $battery->name)),
                        'regular_price' => $price,
                        'categories' => [
                            [
                                'id' => $category[0]->id,
                            ],
                        ],
                        // 'images' => [
                        //     [
                        //         'src' => $battery_image_url,
                        //     ],
                        // ],
                        'attributes' => []
                    ];

                    $this->info('Product ' . $battery->name . ' will be created');
                } else {
                    // Jika produk sudah ada, tambahkan ke array update
                    $productUpdate[] = [
                        'id' => $product[0]->id,
                        'name' => $battery->name,
                        'slug' => strtolower(str_replace(' ', '-', $battery->name)),
                        'regular_price' => $price,
                        'categories' => [
                            [
                                'id' => $category[0]->id,
                            ],
                        ],
                        // 'images' => [
                        //     [
                        //         'src' => $battery_image_url,
                        //     ],
                        // ],

                        // attribute

                    ];

                    $this->info('Product ' . $battery->name . ' will be updated');
                }
            }


            if (!empty($categoryUpdate)) {
                $this->woocommerce->post('products/categories/batch', ['update' => $categoryUpdate]);
            }

            if (!empty($categoryCreate)) {
                $this->woocommerce->post('products/categories/batch', ['create' => $categoryCreate]);
            }

            if (!empty($productUpdate)) {
                $this->woocommerce->post('products/batch', ['update' => $productUpdate]);
            }

            if (!empty($productCreate)) {
                $resultProductCreate = $this->woocommerce->post('products/batch', ['create' => $productCreate]);
            }


            if (isset($resultProductCreate)) {
                // log info
                Log::info('Product : ' . json_encode($productCreate));
                Log::info('Product Created: ' . json_encode($resultProductCreate));
            }

            $this->info('Processed ' . count($batteries) . ' batteries');

            // Increment offset untuk batch berikutnya
            $offset += $limit;
        }
    }


    private function findCategoryWooBySlug($slug)
    {
        $slug = strtolower($slug);
        $slug = str_replace(' ', '-', $slug);
        return $this->woocommerce->get('products/categories', ['slug' => $slug]);
    }

    private function findProductWooBySlug($slug)
    {
        $slug = strtolower($slug);
        $slug = str_replace(' ', '-', $slug);
        return $this->woocommerce->get('products', ['slug' => $slug]);
    }

    private function findProductWooByName($name)
    {
        return $this->woocommerce->get('products', ['search' => $name]);
    }
}
