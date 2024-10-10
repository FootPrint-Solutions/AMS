<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Automattic\WooCommerce\Client;

class DeleteWooAttributes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woo:delete-attributes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all attributes from WooCommerce';
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
        // Hapus semua atribut
        try {
            $attributes = $this->woocommerce->get('products/attributes');
            // foreach ($attributes as $attribute) {
            //     $this->woocommerce->delete("products/attributes/$attribute->id", ['force' => true]);
            //     $this->info("Atribut dengan ID {$attribute->id} telah dihapus.");
            // }

            // Hapus semua terms di setiap atribut
            foreach ($attributes as $attribute) {
                $terms = $this->woocommerce->get("products/attributes/{$attribute->id}/terms");
                foreach ($terms as $term) {
                    $this->woocommerce->delete("products/attributes/{$attribute->id}/terms/{$term->id}", ['force' => true]);
                    $this->info("Term dengan ID {$term->id} pada atribut {$attribute->id} telah dihapus.");
                }
            }

            $this->info('Semua data term dan atribut telah dihapus.');
        } catch (\Throwable $th) {
            $this->error('Gagal menghapus data term dan atribut.');
            $this->error($th->getMessage());
        }
    }
}
