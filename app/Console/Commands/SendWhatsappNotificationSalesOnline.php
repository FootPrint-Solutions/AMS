<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Orders\SalesOnline\SalesOnlineModel;
use App\Models\Orders\SalesOnline\SalesOnlineBatteriesModel;


class SendWhatsappNotificationSalesOnline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:wa-sales-online';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp notifications for online sales';

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

        $data = SalesOnlineModel::where('whatsapp_status', 'failed')->first();

        if (!$data) {
            $this->info('Tidak ada data penjualan online yang perlu diproses.');
            return 0;
        }

        $cartDetails = SalesOnlineBatteriesModel::where('sales_online_id', $data->id)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                ];
            })
            ->toArray();
        if (empty($cartDetails)) {
            $this->error('Tidak ada detail keranjang yang ditemukan untuk penjualan online ini.');
            return 1;
        }

        $salesOnline = SalesOnlineModel::find($data->id);

        if (!$salesOnline) {
            $this->error('Data penjualan online tidak ditemukan.');
            return 1;
        }

        $response = Http::get('https://whatsapp.raden.social/start-session-json', [
            'session' => "admin_ams",
            'scan' => 'false',
        ]);

        if (isset($response['message']) && str_contains($response['message'], 'Session ID :admin_ams is already exist')) {
            $responseData = $response->json();
            if (isset($responseData['message']) && str_contains($responseData['message'], 'is already exist')) {

                $url_send_message = "https://whatsapp.raden.social/send-message";

                $message = "🎉 *Terima kasih atas pesanan Anda, {$data->customer_name}!*\n\n";
                $message .= "Kode pesanan Anda adalah: *{$salesOnline->id}*.\n";
                $message .= "Kami telah menerima pesanan Anda dengan detail berikut:\n\n";
                $message .= "📌 *Informasi Pelanggan:*\n";
                $message .= "👤 Nama: {$data->customer_name}\n";
                $message .= "📍 Alamat: {$data->address}, {$data->sub_district}, {$data->district}, {$data->city}, {$data->province}, {$data->postal_code}\n";
                $message .= "📞 No. HP: {$data->phone_number}\n";
                $message .= "✉️ Email: {$data->email}\n";
                $message .= "🚗 Plat Nomor: {$data->vehicle_plate}\n";
                $message .= "📆 Tanggal Pengiriman: {$data->delivery_date}\n";
                if (!empty($data['additionalInfo'])) {
                    $message .= "📝 Info Tambahan: {$data->additional_info}\n";
                }

                $message .= "\n🛒 *Rincian Pesanan:*\n";
                $totalPayment = 0;
                foreach ($cartDetails as $i => $cart) {
                    $total = $cart['price'] * $cart['quantity'];
                    $totalPayment += $total;
                    $message .= "📦 Produk #" . ($i + 1) . "\n";
                    $message .= "🔸 Nama: {$cart['name']}\n";
                    $message .= "🔸 Harga: Rp" . number_format($cart['price'], 0, ',', '.') . "\n";
                    $message .= "🔸 Jumlah: {$cart['quantity']}\n";
                    $message .= "🔸 Total: Rp" . number_format($total, 0, ',', '.') . "\n\n";
                }

                $message .= "💰 *Total Pembayaran: Rp" . number_format($totalPayment, 0, ',', '.') . "*\n\n";
                $message .= "✅ Kami akan segera menghubungi Anda untuk proses selanjutnya.\n";
                $message .= "🙏 Terima kasih telah berbelanja di *Aki Kita*!\nSalam hangat,\nTim Aki Kita";
                $message .= "\n\n*Pesan ini dikirim secara otomatis. Mohon tidak membalas pesan ini.*";

                foreach ([
                    ['to' => $data['phone_number'], 'session' => "admin_ams", 'text' => $message]
                ] as $payload) {
                    $response = Http::post($url_send_message, $payload);
                    if (!$response->successful() || !isset($response->json()['data']['status']) || $response->json()['data']['status'] != 1) {
                        Log::error('Error sending message: ' . json_encode($payload) . ' - ' . json_encode($response->json()));
                        $this->error('Gagal mengirim pesan.');
                        return 1;
                    } else {
                        Log::info('Pesan berhasil dikirim ke ' . $payload['to']);
                    }
                }

                $this->info('Pesan berhasil dikirim.');

                $salesOnline->whatsapp_status = 'sent';
                $salesOnline->save();
                $this->info('Status penjualan online telah diperbarui menjadi "sent".');
                return 0;
            } else {
                Log::error('Error fetching QR code: ' . json_encode($response->json()));
                $this->error('Gagal mendapatkan QR code 1.');
                return 1;
            }
        } else {
            Log::error('Error fetching QR code: ' . json_encode($response->json()));
            $this->error('Gagal mendapatkan QR code 2.');
            return 1;
        }
    }
}
