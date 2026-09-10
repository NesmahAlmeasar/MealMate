<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Get user wallet details.
     */
    public function getWallet(Request $request)
    {
        $userId = $request->user()->user_id ?? $request->user()->id ?? 1;
        $wallet = $this->accountingService->getOrCreateWallet($userId);

        return response()->json([
            'status' => true,
            'data' => $wallet,
        ]);
    }

    /**
     * Get financial ledger entries (Admin view or User view).
     */
    public function getLedgerEntries(Request $request)
    {
        $userId = $request->user()->user_id ?? $request->user()->id ?? 1;
        $query = DB::table('ledger_entries');

        // Non-admin users only see their own transactions
        $userRole = $request->user()->role ?? 'client';
        if ($userRole !== 'admin') {
            $query->where(function ($q) use ($userId) {
                $q->where('debit_user_id', $userId)
                  ->orWhere('credit_user_id', $userId);
            });
        }

        $entries = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $entries,
        ]);
    }

    /**
     * Submit payout request.
     */
    public function requestPayout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
        ]);

        $userId = $request->user()->user_id ?? $request->user()->id ?? 1;
        $result = $this->accountingService->requestPayout(
            $userId,
            (float)$request->amount,
            $request->bank_name,
            $request->account_number
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Apply penalty deduction (Admin only).
     */
    public function applyPenalty(Request $request)
    {
        $request->validate([
            'target_user_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string',
        ]);

        $adminId = $request->user()->user_id ?? $request->user()->id ?? 1;
        $success = $this->accountingService->applyPenaltyDeduction(
            (int)$request->target_user_id,
            (float)$request->amount,
            $request->reason,
            $adminId
        );

        return response()->json([
            'status' => $success,
            'message' => $success ? 'تم تطبيق الغرامة واقتطاعها بنجاح' : 'فشل تطبيق الغرامة',
        ]);
    }

    /**
     * Get system financial summary overview (Admin only).
     */
    public function getFinancialSummary(Request $request)
    {
        $totalPlatformEarned = DB::table('wallets')
            ->join('users', 'wallets.user_id', '=', 'users.user_id')
            ->where('users.role', 'admin')
            ->sum('total_earned');

        $totalExpenses = DB::table('system_expenses')->sum('amount');
        $totalPendingPayouts = DB::table('payout_requests')->where('status', 'pending')->sum('amount');
        $netProfit = $totalPlatformEarned - $totalExpenses;

        return response()->json([
            'status' => true,
            'data' => [
                'total_platform_earned' => (float)$totalPlatformEarned,
                'total_expenses' => (float)$totalExpenses,
                'total_pending_payouts' => (float)$totalPendingPayouts,
                'net_profit' => (float)$netProfit,
            ],
        ]);
    }
}
