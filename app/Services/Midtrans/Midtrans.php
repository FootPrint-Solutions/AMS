<?php

namespace App\Services\Midtrans;

use Midtrans\Config;

// Model
use App\Models\Servers\ServerPaymentGatewayModel;

class Midtrans
{
    protected $serverKey;
    protected $isProduction;
    protected $isSanitized;
    protected $is3ds;

    public function __construct()
    {
        $this->serverKey = ServerPaymentGatewayModel::where('name', 'MIDTRANS')->first()->server_key ?? '';
        $this->isProduction =  ServerPaymentGatewayModel::where('name', 'MIDTRANS')->first()->is_active ?? '0';
        $this->isSanitized = false;
        $this->is3ds = false;

        $this->_configureMidtrans();
    }

    public function _configureMidtrans()
    {
        Config::$serverKey = $this->serverKey;
        Config::$isProduction = $this->isProduction;
        Config::$isSanitized = $this->isSanitized;
        Config::$is3ds = $this->is3ds;
    }
}
