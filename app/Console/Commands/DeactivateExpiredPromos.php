<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// MODELS
use App\Models\MasterData\Battery\BatteryPriceModel;

class DeactivateExpiredPromos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promos:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate promos that have expired';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $status = true;

        DB::beginTransaction();

        try {
            // Get all expired promos.
            $expiredPromos = DB::table('promos')
                ->where('period_end', '<', $now)
                ->where('status', 1)
                ->get();

            foreach ($expiredPromos as $promo) {
                // Update the status of the expired promo.
                DB::table('promos')
                    ->where('id', $promo->id)
                    ->update(['status' => 0]);

                // Update all affected batteries' price, effectively returning them to default value.
                $prices = BatteryPriceModel::where('promo_id', $promo->id)->get();
                foreach ($prices as $price) {
                    $price->promo_id = null;
                    $price->discount = 0.0;
                    $price->price_net = 0;
                    $status &= $price->save();
                }
            }

            if ($status) {
                DB::commit();
                $this->info('Expired promos have been deactivated and affected battery prices have been updated.');
            } else {
                DB::rollBack();
                $this->error('An error occurred while updating battery prices.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred: ' . $e->getMessage());
        }

        return 0;
    }
}
