<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransNotificationController extends Controller
{
    /**
     * Handle incoming webhook notification from Midtrans.
     */
    public function handle(Request $request, MidtransService $midtransService): JsonResponse
    {
        $payload = $request->all();

        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');

        // 1. Verifikasi signature_key wajib: SHA512(order_id + status_code + gross_amount + server_key)
        if (! $midtransService->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('Midtrans webhook: Invalid signature', [
                'order_id' => $orderId,
                'signature_key' => $signatureKey,
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Invalid signature key',
            ], 403);
        }

        // 2. Cari pesanan via midtrans_order_id (bukan kode_pesanan)
        $pesanan = Pesanan::where('midtrans_order_id', $orderId)->first();

        if (! $pesanan) {
            Log::warning('Midtrans webhook: Pesanan tidak ditemukan', ['order_id' => $orderId]);

            return response()->json([
                'ok' => false,
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }

        // 3. Mapping transaction_status:
        // settlement -> status_pembayaran='terverifikasi'
        // expire -> status_pembayaran='ditolak'
        // cancel / deny -> status_pembayaran='ditolak'
        // pending -> status_pembayaran='menunggu_verifikasi'
        $statusBaru = match ($transactionStatus) {
            'capture', 'settlement' => 'terverifikasi',
            'expire', 'cancel', 'deny' => 'ditolak',
            'pending' => 'menunggu_verifikasi',
            default => $pesanan->status_pembayaran,
        };

        // Simpan pembaruan status dan simpan payload ke midtrans_response untuk riwayat
        $currentResponse = is_array($pesanan->midtrans_response) ? $pesanan->midtrans_response : [];
        $currentResponse['last_notification'] = $payload;
        $currentResponse['last_transaction_status'] = $transactionStatus;
        $currentResponse['notification_received_at'] = now()->toIso8601String();

        $pesanan->update([
            'status_pembayaran' => $statusBaru,
            'midtrans_response' => $currentResponse,
        ]);

        Log::info("Midtrans webhook: Pesanan {$pesanan->kode_pesanan} status updated to {$statusBaru}", [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Notification processed successfully',
            'kode_pesanan' => $pesanan->kode_pesanan,
            'status_pembayaran' => $statusBaru,
        ]);
    }
}
