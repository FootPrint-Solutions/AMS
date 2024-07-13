<?php

namespace App\Services\Midtrans;

use Midtrans\Transaction as MidtransTransaction;
use Midtrans\Snap;

class Transaction extends Midtrans
{
    protected $order;

    public function __construct($order)
    {
        parent::__construct();

        $this->order = $order;
    }

    public function status($id)
    {
        return MidtransTransaction::status($id);
    }

    public function statusB2b($id)
    {
        return MidtransTransaction::statusB2b($id);
    }

    public function approve($id)
    {
        return MidtransTransaction::approve($id);
    }

    public function cancel($id)
    {
        return MidtransTransaction::cancel($id);
    }

    public function expire($id)
    {
        return MidtransTransaction::expire($id);
    }

    public function refund($id, $params)
    {
        return MidtransTransaction::refund($id, $params);
    }

    public function createTransaction($params)
    {
        try {
            // Get Snap Payment Page URL
            $paymentUrl = Snap::createTransaction($params)->redirect_url;
            return $paymentUrl;
            // Redirect to Snap Payment Page
            // header('Location: ' . $paymentUrl);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
