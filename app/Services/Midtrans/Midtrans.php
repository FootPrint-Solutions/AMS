<?php

namespace App\Services\Midtrans;

use Midtrans\Config;

class Midtrans
{
    protected $serverKey;
    protected $isProduction;
    protected $isSanitized;
    protected $is3ds;

    public function __construct()
    {
        $this->serverKey = "SB-Mid-server-mq4uqrualVrHP-Oqe-eKTWbq";
        $this->isProduction = false;
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
