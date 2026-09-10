<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountingService
{
    /**
     * الحصول على أو إنشاء محفظة المستخدم
     */
    public function getOrCreateWallet(int $userId): object
    {
        $wallet = DB::table('wallets')->where('user_id', $userId)->first();
        if (!$wallet) {
            $walletId = DB::table('wallets')->insertGetId([
                'user_id' => $userId,
                'balance' => 0.00,
                'total_earned' => 0.00,
                'total_withdrawn' => 0.00,
                'pending_withdrawal' => 0.00,
                'updated_at' => now(),
            ]);
            $wallet = DB::table('wallets')->where('wallet_id', $walletId)->first();
        }
        return $wallet;
    }

    /**
     * تسجيل قيد اليومية وتحديث رصيد المحافظ
     */
    public function recordLedgerEntry(
        int $debitUserId,
        int $creditUserId,
        float $amount,
        string $entryType,
        ?string $description = null,
        ?int $adminId = null,
        ?string $ref = null
    ): int {
        return DB::transaction(function () use ($debitUserId, $creditUserId, $amount, $entryType, $description, $adminId, $ref) {
            $transactionRef = $ref ?? ('REF_' . time() . '_' . rand(1000, 9999));

            // 1. تسجيل القيد في جدول القيود
            $entryId = DB::table('ledger_entries')->insertGetId([
                'transaction_ref' => $transactionRef,
                'debit_user_id' => $debitUserId,
                'credit_user_id' => $creditUserId,
                'amount' => $amount,
                'entry_type' => $entryType,
                'description' => $description,
                'created_by_admin_id' => $adminId,
                'created_at' => now(),
            ]);

            // 2. تحديث محفظة الحساب الدائن (إضافة رصيد)
            if ($creditUserId > 0) {
                $this->getOrCreateWallet($creditUserId);
                DB::table('wallets')->where('user_id', $creditUserId)->increment('balance', $amount);
                DB::table('wallets')->where('user_id', $creditUserId)->increment('total_earned', $amount);
            }

            // 3. تحديث محفظة الحساب المدين (خصم رصيد)
            if ($debitUserId > 0) {
                $this->getOrCreateWallet($debitUserId);
                DB::table('wallets')->where('user_id', $debitUserId)->decrement('balance', $amount);
            }

            return $entryId;
        });
    }

    /**
     * احتساب قيد طلبية وجبة عند اكتمالها
     */
    public function processMealOrderAccounting(int $orderId, int $restaurantUserId, float $totalOrderAmount, float $commissionRatePercentage, int $customerUserId, bool $isCod = false)
    {
        $adminUser = DB::table('users')->where('role_id', 1)->first();
        $adminUserId = $adminUser ? $adminUser->user_id : 1;

        $commissionAmount = round(($totalOrderAmount * $commissionRatePercentage) / 100, 2);
        $restaurantNetAmount = $totalOrderAmount - $commissionAmount;

        if ($isCod) {
            // طلبية دفع عند الاستلام (COD): المطعم استلم المبلغ كاملاً نقداً من الزبون، فيصبح مدين للمنصة بقيمة العمولة
            $this->recordLedgerEntry(
                $restaurantUserId,
                $adminUserId,
                $commissionAmount,
                'MEAL_COMMISSION',
                "عمولة منصة من طلبية COD رقم #{$orderId} بنسبة {$commissionRatePercentage}%",
                null,
                "ORDER_COD_{$orderId}"
            );
        } else {
            // دفع إلكتروني (Online): المنصة استلمت المبلغ، وتضيف للمطعم صافيه
            $this->recordLedgerEntry(
                $adminUserId,
                $restaurantUserId,
                $restaurantNetAmount,
                'MEAL_COMMISSION',
                "صافي مبيعات طلبية إلكترونية رقم #{$orderId} للمطعم بعد خصم عمولة المنصة {$commissionRatePercentage}%",
                null,
                "ORDER_ONLINE_{$orderId}"
            );
        }
    }

    /**
     * إنشاء طلب سحب أرباح جديد
     */
    public function requestPayout(int $userId, float $amount, ?string $bankName = null, ?string $accountNumber = null): array
    {
        $wallet = $this->getOrCreateWallet($userId);
        if ($wallet->balance < $amount) {
            return [
                'success' => false,
                'message' => 'الرصيد المتاح غير كافٍ لإتمام عملية السحب',
            ];
        }

        DB::transaction(function () use ($userId, $amount, $bankName, $accountNumber) {
            // خصم من الرصيد المتاح ونقله إلى قيد الانتظار
            DB::table('wallets')->where('user_id', $userId)->decrement('balance', $amount);
            DB::table('wallets')->where('user_id', $userId)->increment('pending_withdrawal', $amount);

            DB::table('payout_requests')->insert([
                'user_id' => $userId,
                'amount' => $amount,
                'bank_name' => $bankName,
                'account_number' => $accountNumber,
                'status' => 'pending',
                'requested_at' => now(),
            ]);
        });

        return [
            'success' => true,
            'message' => 'تم تقديم طلب سحب الأرباح بنجاح وهو قيد المراجعة',
        ];
    }

    /**
     * تطبيق غرامة على مستخدم / مطعم
     */
    public function applyPenaltyDeduction(int $userId, float $amount, string $reason, ?int $adminId = null): bool
    {
        $adminUser = DB::table('users')->where('role_id', 1)->first();
        $adminUserId = $adminUser ? $adminUser->user_id : ($adminId ?? 1);

        $this->recordLedgerEntry(
            $userId,
            $adminUserId,
            $amount,
            'PENALTY_DEDUCTION',
            $reason,
            $adminId,
            'PENALTY_' . time() . '_' . rand(100, 999)
        );

        return true;
    }

    /**
     * معالجة النجاح عند اكتمال الدفع من البوابة
     */
    public function recordPaymentSuccess(object $payment): void
    {
        $payableType = $payment->payable_type ?? 'CART';
        $payableId = $payment->payable_id ?? null;
        $amount = (float)($payment->amount ?? 0);
        $userId = $payment->user_id ?? 1;
        $txRef = $payment->transaction_id ?? ('TX_' . time());

        $adminUser = DB::table('users')->where('role_id', 1)->first();
        $adminUserId = $adminUser ? $adminUser->user_id : 1;

        if ($payableType === 'WALLET_REFILL') {
            $this->recordLedgerEntry(
                $adminUserId,
                $userId,
                $amount,
                'WALLET_REFILL',
                "إعادة شحن المحفظة عبر بوابة بس الإلكترونية (معاملة: {$txRef})",
                null,
                $txRef
            );
        } elseif ($payableType === 'CART' && $payableId) {
            $cart = DB::table('cart')->where('cart_id', $payableId)->first();
            if ($cart) {
                $restaurant = DB::table('restaurants')->where('restaurants_id', $cart->restaurants_id)->first();
                $managerUserId = $restaurant ? ($restaurant->manager_id ?? $adminUserId) : $adminUserId;
                $commissionRate = $restaurant ? (float)($restaurant->commission_rate ?? 10.0) : 10.0;

                $this->processMealOrderAccounting(
                    $payableId,
                    $managerUserId,
                    $amount,
                    $commissionRate,
                    $userId,
                    false
                );

                DB::table('cart')->where('cart_id', $payableId)->update([
                    'state' => 'paid',
                    'updated_at' => now(),
                ]);
            }
        } elseif ($payableType === 'CONSULTING' && $payableId) {
            $consultation = DB::table('consultations')->where('consultation_id', $payableId)->first();
            $specialistUserId = $consultation ? ($consultation->nutritionist_id ?? $adminUserId) : $adminUserId;

            $platformFee = round($amount * 0.15, 2); // 15% عمولة منصة
            $specialistFee = $amount - $platformFee;

            $this->recordLedgerEntry(
                $adminUserId,
                $specialistUserId,
                $specialistFee,
                'CONSULTATION_FEE',
                "صافي رسوم الاستشارة #{$payableId}",
                null,
                $txRef
            );

            if ($consultation) {
                DB::table('consultations')->where('consultation_id', $payableId)->update([
                    'payment_status' => 'paid',
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * موافقة وتأكيد السحب بواسطة الأدمن
     */
    public function approvePayout(int $payoutId, int $adminId, ?string $adminNotes = null): bool
    {
        $payout = DB::table('payout_requests')->where('payout_id', $payoutId)->first();
        if (!$payout || $payout->status !== 'pending') {
            return false;
        }

        DB::transaction(function () use ($payout, $adminId, $adminNotes) {
            // تحديث حالة الطلب
            DB::table('payout_requests')->where('payout_id', $payout->payout_id)->update([
                'status' => 'completed',
                'admin_notes' => $adminNotes,
                'processed_at' => now(),
            ]);

            // خصم المعلق وإعادة احتساب المسحوبات الكلية
            DB::table('wallets')->where('user_id', $payout->user_id)->decrement('pending_withdrawal', $payout->amount);
            DB::table('wallets')->where('user_id', $payout->user_id)->increment('total_withdrawn', $payout->amount);

            // تسجيل قيد سحب أرباح
            $adminUser = DB::table('users')->where('role_id', 1)->first();
            $adminUserId = $adminUser ? $adminUser->user_id : $adminId;

            $this->recordLedgerEntry(
                $adminUserId,
                $payout->user_id,
                $payout->amount,
                'PAYOUT_WITHDRAWAL',
                "تأكيد سحب أرباح عبر الحساب البنكي / النقدي: {$payout->bank_name} - {$payout->account_number}",
                $adminId,
                "PAYOUT_{$payout->payout_id}"
            );
        });

        return true;
    }
}
