<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


// Models
use App\Models\Orders\SalesOrder\SalesOrderModel;

// Midtrans 
use App\Services\Midtrans\CreateSnapTokenService;

class Midtrans extends Controller
{
    public function notificationHandler(Request $request)
    {
        $json_result = file_get_contents('php://input');
        $result = json_decode($json_result);

        Log::info('Midtrans Notification Handler', ['result' => $result]);

        if ($result->status_code == 200) {
            $order_id = $result->order_id;
            $transaction_status = $result->transaction_status;
            $fraud_status = $result->fraud_status;

            if ($transaction_status == 'capture') {
                if ($fraud_status == 'challenge') {
                    // TODO set transaction status on your database to 'challenge'
                    // and response with 200 OK
                    Log::info('Transaction Challenge', ['order_id' => $order_id]);
                } else if ($fraud_status == 'accept') {
                    // TODO set transaction status on your database to 'success'
                    // and response with 200 OK
                    Log::info('Transaction Success', ['order_id' => $order_id]);
                }
            } else if ($transaction_status == 'settlement') {
                // TODO set transaction status on your database to 'success'
                // and response with 200 OK

                // Update order status
                $order = SalesOrderModel::where('sales_order_number', $order_id)->first();
                $order->status = 'paid';
                $order->save();

                Log::info('Transaction Settlement', ['order_id' => $order_id]);
            } else if (
                $transaction_status == 'cancel' ||
                $transaction_status == 'deny' ||
                $transaction_status == 'expire'
            ) {
                // TODO set transaction status on your database to 'failure'
                // and response with 200 OK

                // Update order status
                $order = SalesOrderModel::where('sales_order_number', $order_id)->first();
                $order->status =  $transaction_status;
                $order->save();

                Log::info('Transaction Failure', ['order_id' => $order_id]);
            } else if ($transaction_status == 'pending') {
                // TODO set transaction status on your database to 'pending' / waiting payment
                // and response with 200 OK

                // Update order status
                $order = SalesOrderModel::where('sales_order_number', $order_id)->first();
                $order->status = 'pending';
                $order->save();

                Log::info('Transaction Pending', ['order_id' => $order_id]);
            }
        }
    }
}
