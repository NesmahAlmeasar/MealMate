<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BasGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected BasGatewayService $basGatewayService;

    public function __construct(BasGatewayService $basGatewayService)
    {
        $this->basGatewayService = $basGatewayService;
    }

    /**
     * Initiate payment checkout session via BAS Gateway.
     */
    public function initiateCheckout(Request $request)
    {
        $request->validate([
            'payable_type' => 'required|in:CART,CONSULTING,WALLET_REFILL',
            'payable_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
        ]);

        $userId = $request->user()->user_id ?? $request->user()->id ?? $request->input('user_id', 1);
        $payableType = $request->input('payable_type');
        $payableId = (int)$request->input('payable_id');
        $amount = (float)$request->input('amount');
        $description = $request->input('description', "Payment for {$payableType} #{$payableId}");

        $result = $this->basGatewayService->createCheckoutSession(
            $payableType,
            $payableId,
            $userId,
            $amount,
            $description
        );

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء جلسة الدفع بنجاح',
            'data' => $result,
        ]);
    }

    /**
     * Handle Server-to-Server Webhook callback from BAS Gateway.
     */
    public function handleWebhook(Request $request)
    {
        Log::info('BAS Gateway Webhook received: ', $request->all());

        $payload = $request->all();
        $processed = $this->basGatewayService->processWebhook($payload);

        return response()->json([
            'status' => $processed,
            'message' => $processed ? 'Webhook processed successfully' : 'Webhook processing failed',
        ]);
    }

    /**
     * Check payment status by transaction ID.
     */
    public function checkStatus($transactionId)
    {
        $payment = DB::table('payments')->where('transaction_id', $transactionId)->first();

        if (!$payment) {
            return response()->json([
                'status' => false,
                'message' => 'معاملة الدفع غير موجودة',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'transaction_id' => $payment->transaction_id,
                'payment_status' => $payment->status,
                'amount' => $payment->amount,
                'payable_type' => $payment->payable_type,
                'payable_id' => $payment->payable_id,
                'updated_at' => $payment->updated_at,
            ],
        ]);
    }

    /**
     * Mock Payment Success Endpoint (For Local Development & Mobile App Simulation).
     */
    public function mockPaySuccess($transactionId)
    {
        $payload = [
            'order_id' => $transactionId,
            'status' => 'paid',
            'paid_at' => now()->toIso8601String(),
        ];

        $processed = $this->basGatewayService->processWebhook($payload);

        return response()->json([
            'status' => $processed,
            'message' => 'تم محاكاة عملية الدفع بنجاح وتسجيل القيد المحاسبي',
            'transaction_id' => $transactionId,
        ]);
    }
}
