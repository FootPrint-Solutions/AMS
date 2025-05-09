<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Orders\SalesOnline\SalesOnlineModel;
use App\Models\Orders\SalesOnline\SalesOnlineBatteriesModel;
use App\Models\MasterData\Battery\BatteryModel;

class SalesOnline extends Controller
{
    public function receiveData(Request $request)
    {
        try {
            if (!$request->isMethod('post')) {
                return response()->json(['status' => 'error', 'message' => 'Invalid request method'], 405);
            }

            $data = $request->only([
                'customerName', 'province', 'city', 'district', 'subDistrict',
                'postalCode', 'phoneNumber', 'email', 'vehiclePlate', 'deliveryDate', 'additionalInfo'
            ]);

            $cartDetails = $request->input('cartDetails', []);

            // Validasi data
            if (
                empty($data['customerName']) || empty($data['province']) || empty($data['city']) ||
                empty($data['district']) || empty($data['subDistrict']) || empty($data['phoneNumber']) ||
                empty($data['deliveryDate'])
            ) {
                return response()->json(['status' => 'error', 'message' => 'Semua kolom yang wajib diisi harus diisi.']);
            }

            if (!preg_match('/^[0-9]{10,15}$/', $data['phoneNumber'])) {
                return response()->json(['status' => 'error', 'message' => 'Nomor telepon tidak valid. Harap gunakan format 628xxxxxxxxx.']);
            }

            $salesOnline = SalesOnlineModel::create([
                'customer_name' => $data['customerName'],
                'province' => $data['province'],
                'city' => $data['city'],
                'district' => $data['district'],
                'sub_district' => $data['subDistrict'],
                'postal_code' => $data['postalCode'],
                'phone_number' => $data['phoneNumber'],
                'email' => $data['email'],
                'vehicle_plate' => $data['vehiclePlate'],
                'delivery_date' => $data['deliveryDate'],
                'additional_info' => $data['additionalInfo'],
            ]);

            foreach ($cartDetails as $cart) {
                SalesOnlineBatteriesModel::create([
                    'sales_online_id' => $salesOnline->id,
                    'battery_id' => $cart['id'],
                    'name' => $cart['name'],
                    'price' => $cart['price'],
                    'image' => $cart['image'],
                    'quantity' => $cart['quantity'],
                    'total_price' => $cart['price'] * $cart['quantity'],
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan.', 'data' => $salesOnline]);

            try {
                $response = Http::get('https://whatsapp.akikita.web.id/start-session-json', [
                    'session' => "admin_ams",
                    'scan' => 'true',
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    if (isset($responseData['message']) && str_contains($responseData['message'], 'is already exist')) {

                        $url_send_message = "https://whatsapp.akikita.web.id/send-message";

                        $message = "Ada pesanan baru dari " . $data['customerName'] . "\n";
                        $message .= "Detail Pesanan:\n";
                        foreach ([
                            'Nama' => $data['customerName'],
                            'Provinsi' => $data['province'],
                            'Kota' => $data['city'],
                            'Kecamatan' => $data['district'],
                            'Desa' => $data['subDistrict'],
                            'Kode Pos' => $data['postalCode'],
                            'Nomor Telepon' => $data['phoneNumber'],
                            'Email' => $data['email'],
                            'Plat Nomor' => $data['vehiclePlate'],
                            'Tanggal Pengiriman' => $data['deliveryDate'],
                            'Info Tambahan' => $data['additionalInfo']
                        ] as $key => $value) {
                            $message .= "$key: $value\n";
                        }

                        $message .= "Detail Pesanan:\n";
                        $totalPayment = 0;
                        foreach ($cartDetails as $cart) {
                            $totalPrice = $cart['price'] * $cart['quantity'];
                            $totalPayment += $totalPrice;
                            foreach ([
                                'Nama' => $cart['name'],
                                'Harga' => $cart['price'],
                                'Gambar' => $cart['image'],
                                'Jumlah' => $cart['quantity'],
                                'Total Harga' => $totalPrice
                            ] as $key => $value) {
                                $message .= "$key: $value\n";
                            }
                        }
                        $message .= "Total Pembayaran: $totalPayment\n";
                        $message .= "Silakan konfirmasi pesanan ini.\nTerima kasih telah berbelanja di kami.\nSalam, Aki Kita";

                        foreach ([
                            ['to' => $data['phoneNumber'], 'session' => auth()->user()->username, 'text' => $message],
                            ['to' => '6281563532934', 'session' => auth()->user()->username, 'text' => $message]
                        ] as $payload) {
                            $response = Http::post($url_send_message, $payload);
                            if (!$response->successful() || !isset($response->json()['data']['status']) || $response->json()['data']['status'] != 1) {
                                return response()->json(['status' => 'error', 'message' => 'Gagal mengirim pesan.']);
                            }
                        }

                        return response()->json(['status' => 'success', 'message' => 'Pesan berhasil dikirim.']);
                    }
                }
            } catch (\Throwable $th) {
                Log::error('Error fetching QR code: ' . $th->getMessage());
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data: ' . $th->getMessage()], 500);
        }
    }
}
