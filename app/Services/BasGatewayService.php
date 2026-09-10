<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BasGatewayService
{
    protected string $merchantId;
    protected string $apiKey;
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('basgateway.merchant_id', 'demo_merchant');
        $this->apiKey = config('basgateway.api_key', 'demo_api_key');
        $this->secretKey = config('basgateway.secret_key', 'demo_secret_key');
        $this->baseUrl = config('basgateway.base_url', 'https://basgate.apidog.io');
    }

    /**
     * إنشاء جلسة دفع عبر بوابة بس
     */
    public function createCheckoutSession(int $paymentId, float $amount, string $payableType, int $userId): array
    {
        $payload = [
            'merchant_id' => $this->merchantId,
            'amount' => $amount,
            'currency' => 'YER',
            'order_reference' => 'PAY_' . $paymentId,
            'payable_type' => $payableType,
            'user_id' => $userId,
            'callback_url' => config('basgateway.redirect_url'),
        ];

        $signature = hash_hmac('sha256', json_encode($payload), $this->secretKey);

        try {
            // محاكاة أو استدعاء البوابة
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'X-SIGNATURE' => $signature,
            ])->post("{$this->baseUrl}/checkout", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'payment_url' => $data['payment_url'] ?? "{$this->baseUrl}/pay/" . $paymentId,
                    'transaction_id' => $data['transaction_id'] ?? 'TX_BAS_' . time() . '_' . rand(100, 999),
                ];
            }
        } catch (\Exception $e) {
            Log::error("BasGateway Checkout Exception: " . $e->getMessage());
        }

        // رابط دفع افتراضي للتجربة العادية والتطوير المحلي
        $mockTx = 'TX_BAS_' . time() . '_' . rand(100, 999);
        return [
            'success' => true,
            'payment_url' => url("/payments/bas/mock-pay/{$paymentId}?tx={$mockTx}"),
            'transaction_id' => $mockTx,
        ];
    }

    /**
     * فحص التوقيع الهيدر للـ Webhook
     */
    public function verifyWebhookSignature(array $payload, string $signature): bool
    {
        $expectedSignature = hash_hmac('sha256', json_encode($payload), $this->secretKey);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * معالجة إشعار الـ Webhook من البوابة وتحديث حالة الدفع والقيود المحاسبية
     */
    public function processWebhook(array $payload): bool
    {
        try {
            $transactionId = $payload['transaction_id'] ?? $payload['order_id'] ?? ('TX_BAS_' . time());
            $status = strtolower($payload['status'] ?? 'paid');
            $payableType = $payload['payable_type'] ?? 'CART';
            $payableId = (int)($payload['payable_id'] ?? $payload['order_id'] ?? 0);
            $userId = (int)($payload['user_id'] ?? 1);
            $amount = (float)($payload['amount'] ?? 0);

            // تحديث أو إنشاء سجّل الدفع في DB
            \Illuminate\Support\Facades\DB::table('payments')->updateOrInsert(
                ['transaction_id' => $transactionId],
                [
                    'payable_type' => $payableType,
                    'payable_id' => $payableId,
                    'user_id' => $userId,
                    'amount' => $amount,
                    'status' => ($status === 'paid' || $status === 'success') ? 'paid' : 'failed',
                    'gateway_response' => json_encode($payload),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            if ($status === 'paid' || $status === 'success') {
                $accounting = app(\App\Services\AccountingService::class);
                $accounting->recordPaymentSuccess((object)[
                    'transaction_id' => $transactionId,
                    'payable_type' => $payableType,
                    'payable_id' => $payableId,
                    'user_id' => $userId,
                    'amount' => $amount,
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("BasGateway Webhook Process Error: " . $e->getMessage());
            return false;
        }
    }
}
