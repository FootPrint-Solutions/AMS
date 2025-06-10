<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Orders\SalesOnline\SalesOnlineModel;
use App\Models\Orders\SalesOnline\SalesOnlineBatteriesModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\Servers\ServerWhatsappModel;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SalesOnline extends Controller
{
    public function receiveData(Request $request)
    {
        try {

            $whatsappAdmin =  ServerWhatsappModel::where('name', 'AMS WA')->first();

            if (!$request->isMethod('post')) {
                return response()->json(['status' => 'error', 'message' => 'Invalid request method'], 405);
            }

            $data = $request->only([
                'customerName', 'province', 'city', 'district', 'subDistrict',
                'postalCode', 'phoneNumber', 'email', 'vehiclePlate', 'deliveryDate', 'additionalInfo', 'alamatLengkap', 'ipAddress', 'userAgent', 'latitude', 'longitude'
            ]);

            $cartDetails = $request->input('cartDetails', []);

            // Validasi data
            if (
                empty($data['customerName']) || empty($data['province']) || empty($data['city']) ||
                empty($data['district']) || empty($data['subDistrict']) || empty($data['phoneNumber']) ||
                empty($data['deliveryDate']) || empty($data['alamatLengkap'])
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
                'address' => $data['alamatLengkap'],
                'whatsapp_status' => 'pending',
                'ip_address' => $data['ipAddress'] ?? $request->ip(),
                'user_agent' => $data['userAgent'] ?? $request->header('User-Agent'),
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
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

            // $response = Http::get('https://whatsapp.ekakosmetikcirebon.id/start-session-json', [
            //     'session' => "admin_ams",
            //     'scan' => 'false',
            // ]);

            // if (isset($response['message']) && str_contains($response['message'], 'Session ID :admin_ams is already exist')) {
            //     $responseData = $response->json();
            //     if (isset($responseData['message']) && str_contains($responseData['message'], 'is already exist')) {

            //         $url_send_message = "https://whatsapp.ekakosmetikcirebon.id/send-message";

            //         $message = "🎉 *Terima kasih atas pesanan Anda, {$data['customerName']}!*\n\n";
            //         $message .= "Kode pesanan Anda adalah: *{$salesOnline->id}*.\n";
            //         $message .= "Kami telah menerima pesanan Anda dengan detail berikut:\n\n";
            //         $message .= "📌 *Informasi Pelanggan:*\n";
            //         $message .= "👤 Nama: {$data['customerName']}\n";
            //         $message .= "📍 Alamat: {$data['alamatLengkap']}, {$data['subDistrict']}, {$data['district']}, {$data['city']}, {$data['province']}, {$data['postalCode']}\n";
            //         $message .= "📞 No. HP: {$data['phoneNumber']}\n";
            //         $message .= "✉️ Email: {$data['email']}\n";
            //         $message .= "🚗 Plat Nomor: {$data['vehiclePlate']}\n";
            //         $message .= "📆 Tanggal Pengiriman: {$data['deliveryDate']}\n";
            //         if (!empty($data['additionalInfo'])) {
            //             $message .= "📝 Info Tambahan: {$data['additionalInfo']}\n";
            //         }

            //         $message .= "\n🛒 *Rincian Pesanan:*\n";
            //         $totalPayment = 0;
            //         foreach ($cartDetails as $i => $cart) {
            //             $total = $cart['price'] * $cart['quantity'];
            //             $totalPayment += $total;
            //             $message .= "📦 Produk #" . ($i + 1) . "\n";
            //             $message .= "🔸 Nama: {$cart['name']}\n";
            //             $message .= "🔸 Harga: Rp" . number_format($cart['price'], 0, ',', '.') . "\n";
            //             $message .= "🔸 Jumlah: {$cart['quantity']}\n";
            //             $message .= "🔸 Total: Rp" . number_format($total, 0, ',', '.') . "\n\n";
            //         }

            //         $message .= "💰 *Total Pembayaran: Rp" . number_format($totalPayment, 0, ',', '.') . "*\n\n";
            //         $message .= "✅ Kami akan segera menghubungi Anda untuk proses selanjutnya.\n";
            //         $message .= "🙏 Terima kasih telah berbelanja di *Aki Kita*!\nSalam hangat,\nTim Aki Kita";
            //         $message .= "\n\n*Pesan ini dikirim secara otomatis. Mohon tidak membalas pesan ini.*";


            //         $message_admin = "📥 *Pesanan Baru Masuk!*\n\n";
            //         $message_admin .= "📌 *Data Pelanggan:*\n";
            //         $message_admin .= "👤 Nama: {$data['customerName']}\n";
            //         $message_admin .= "📍 Alamat: {$data['alamatLengkap']}, {$data['subDistrict']}, {$data['district']}, {$data['city']}, {$data['province']}, {$data['postalCode']}\n";
            //         $message_admin .= "📞 No. HP: {$data['phoneNumber']}\n";
            //         $message_admin .= "✉️ Email: {$data['email']}\n";
            //         $message_admin .= "🚗 Plat Nomor: {$data['vehiclePlate']}\n";
            //         $message_admin .= "📆 Tanggal Pengiriman: {$data['deliveryDate']}\n";
            //         if (!empty($data['additionalInfo'])) {
            //             $message_admin .= "📝 Info Tambahan: {$data['additionalInfo']}\n";
            //         }

            //         $message_admin .= "\n🛒 *Detail Pesanan:*\n";
            //         $totalPayment = 0;
            //         foreach ($cartDetails as $i => $cart) {
            //             $total = $cart['price'] * $cart['quantity'];
            //             $totalPayment += $total;
            //             $message_admin .= "📦 Produk #" . ($i + 1) . "\n";
            //             $message_admin .= "🔸 Nama: {$cart['name']}\n";
            //             $message_admin .= "🔸 Harga: Rp" . number_format($cart['price'], 0, ',', '.') . "\n";
            //             $message_admin .= "🔸 Jumlah: {$cart['quantity']}\n";
            //             $message_admin .= "🔸 Total: Rp" . number_format($total, 0, ',', '.') . "\n\n";
            //         }
            //         $message_admin .= "💰 *Total Pembayaran: Rp" . number_format($totalPayment, 0, ',', '.') . "*\n";
            //         $message_admin .= "🚨 Segera proses pesanan ini melalui sistem!\n";
            //         $message_admin .= "📱 Dikirim dari sistem *Aki Kita*";


            //         foreach ([
            //             ['to' => $data['phoneNumber'], 'session' => "admin_ams", 'text' => $message]
            //             // ['to' =>  $whatsappAdmin['number'], 'session' => "admin_ams", 'text' => $message_admin]
            //         ] as $payload) {
            //             $response = Http::post($url_send_message, $payload);
            //             if (!$response->successful() || !isset($response->json()['data']['status']) || $response->json()['data']['status'] != 1) {
            //                 return response()->json(['status' => 'error', 'message' => 'Gagal mengirim pesan. ' . json_encode($response->json())]);
            //                 Log::error('Error sending message: ' . $response->json());
            //             } else {
            //                 Log::info('Pesan berhasil dikirim ke ' . $payload['to']);
            //             }
            //         }

            //         return response()->json(['status' => 'success', 'message' => 'Pesan berhasil dikirim.', 'data' => $response->json()]);
            //         Log::info('Pesan berhasil dikirim ke ' . $data['phoneNumber']);
            //     } else {
            //         return response()->json(['status' => 'error', 'message' => 'Gagal mendapatkan QR code 1.']);
            //         Log::error('Error fetching QR code: ' . $response->json());
            //     }
            // } else {
            //     return response()->json(['status' => 'error', 'message' => 'Gagal mendapatkan QR code 2. ' . $response->json() . ' ']);
            //     Log::error('Error fetching QR code: ' . $response->json());
            // }

            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan.', 'data' => $salesOnline]);
            Log::info('Data berhasil disimpan.', ['data' => $salesOnline]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data: ' . $th->getMessage()], 500);
        }
    }
}
