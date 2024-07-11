<?php

namespace App\Services\Midtrans;

use Midtrans\Snap;

class CreateSnapTokenService extends Midtrans
{
    protected $order;

    public function __construct($order)
    {
        parent::__construct();

        $this->order = $order;
    }

    public function getSnapToken()
    {
        $params = [
            'transaction_details' => [
                'order_id' => $this->order->number,
                'gross_amount' => $this->order->total_price,
            ],
            'item_details' => [
                [
                    'id' => 1,
                    'price' => '150000',
                    'quantity' => 1,
                    'name' => 'Flashdisk Toshiba 32GB',
                ],
                [
                    'id' => 2,
                    'price' => '60000',
                    'quantity' => 2,
                    'name' => 'Memory Card VGEN 4GB',
                ],
            ],
            'customer_details' => [
                'first_name' => 'Martin Mulyo Syahidin',
                'email' => 'mulyosyahidin95@gmail.com',
                'phone' => '081234567890',
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        return $snapToken;
    }

    public function getSnapTokenUrl($data)
    {
        $transaction_details = array(
            'order_id' => $data['InvoiceNumber'],
            'gross_amount' => $data['TotalAmount'], // no decimal allowed for creditcard
        );

        // check if phone number not start with '0' and replace it with '62'
        if (substr($data['ContactNumber'], 0, 1) == '0') {
            $data['ContactNumber'] = '62' . substr($data['ContactNumber'], 1);
        } else {
            $data['ContactNumber'] = '62' . $data['ContactNumber'];
        }

        // Populate customer's billing address
        $billing_address = array(
            'first_name'   => $data['Fullname'],
            'last_name'    => "",
            'address'      => $data['AddressCustomer'],
            'city'         => "",
            'postal_code'  => "",
            'phone'        => $data['ContactNumber'],
            'country_code' => 'IDN'
        );

        // Populate customer's shipping address
        $shipping_address = array(
            'first_name'   => $data['Fullname'],
            'last_name'    => "",
            'address'      => $data['AddressCustomer'],
            'city'         => "",
            'postal_code'  => "",
            'phone'        => $data['ContactNumber'],
            'country_code' => 'IDN'
        );

        // Populate customer's info
        $customer_details = array(
            'first_name'       => $data['Fullname'],
            'last_name'        => "",
            'email'            => $data['EmailCustomer'],
            'phone'            => $data['ContactNumber'],
            'billing_address'  => $billing_address,
            'shipping_address' => $shipping_address
        );
        // dd($data['dataProduct']);
        // item details
        $items = array();
        foreach ($data['dataProduct'] as $item) {
            $price = str_replace('.', '', $item['SubtotalRow']);
            $items[] = array(
                'id'       => rand(1, 1000), // generate random id
                'price'    =>  $price,
                'quantity' => $item['qty'],
                'name'     => $item['name']
            );
        }

        $params = array(
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $items,
        );

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
