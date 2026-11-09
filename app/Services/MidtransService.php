<?php

namespace App\Services;

use Exception;
use Midtrans\Config;
use Midtrans\CoreApi;

class MidtransService
{
    public function __construct()
    {
        $this->bootConfig();
    }

    /**
     * Inisialisasi konfigurasi Midtrans SDK.
     */
    protected function bootConfig(): void
    {
        Config::$serverKey = (string) config('services.midtrans.server_key');
        Config::$clientKey = (string) config('services.midtrans.client_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production', false);
        Config::$isSanitized = (bool) config('services.midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('services.midtrans.is_3ds', true);

        // Pastikan cURL menggunakan CA bundle bawaan Midtrans / Laragon untuk mencegah SSL certificate error di Windows
        $cacertPath = base_path('vendor/midtrans/midtrans-php/data/cacert.pem');
        $caFile = file_exists($cacertPath) ? $cacertPath : (file_exists('C:/laragon/etc/ssl/cacert.pem') ? 'C:/laragon/etc/ssl/cacert.pem' : null);

        Config::$curlOptions = [
            CURLOPT_HTTPHEADER => ['User-Agent: Hamta-Healthy-Food/1.0'],
        ];

        if ($caFile) {
            Config::$curlOptions[CURLOPT_CAINFO] = $caFile;
        }
    }

    /**
     * Buat transaksi QRIS menggunakan Midtrans Core API.
     *
     * @param  string  $orderId  ID transaksi unik Midtrans
     * @param  int  $grossAmount  Total tagihan bulat (rupiah)
     * @return array Raw response Midtrans beserta helper qr_url
     *
     * @throws Exception
     */
    public function chargeQris(string $orderId, int $grossAmount): array
    {
        $this->bootConfig();

        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'qris' => [
                'acquirer' => 'gopay',
            ],
        ];

        $responseObj = CoreApi::charge($params);

        // Ubah response stdClass menjadi array associative
        $response = json_decode(json_encode($responseObj), true);

        if (isset($response['status_code']) && ! in_array((string) $response['status_code'], ['200', '201'])) {
            throw new Exception($response['status_message'] ?? 'Midtrans charge failed with status '.$response['status_code']);
        }

        // Ekstrak URL gambar QR code jika tersedia di actions
        $qrUrl = null;
        if (! empty($response['actions']) && is_array($response['actions'])) {
            foreach ($response['actions'] as $action) {
                if (($action['name'] ?? '') === 'generate-qr-code') {
                    $qrUrl = $action['url'] ?? null;
                    break;
                }
            }
        }

        if ($qrUrl) {
            $response['qr_url'] = $qrUrl;
        }

        return $response;
    }

    /**
     * Verifikasi signature key dari notifikasi Midtrans.
     * Rumus: SHA512(order_id + status_code + gross_amount + server_key)
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = (string) config('services.midtrans.server_key');
        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($expected, $signatureKey);
    }
}
