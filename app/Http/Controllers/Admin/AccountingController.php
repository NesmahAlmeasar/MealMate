<?php

namespace App\Http\Controllers\Admin;

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
     * عرض شاشة الدفتر المحاسبي العام والمحافظ الموحدة
     */
    public function index(Request $request)
    {
        // إحصائيات عامة
        $totalWalletsBalance = DB::table('wallets')->sum('balance');
        $totalEarnedAll = DB::table('wallets')->sum('total_earned');
        $totalWithdrawnAll = DB::table('wallets')->sum('total_withdrawn');
        $totalPendingPayouts = DB::table('payout_requests')->where('status', 'pending')->sum('amount');

        // القيود المحاسبية الأخيرة
        $entries = DB::table('ledger_entries')
            ->leftJoin('users as debit_u', 'ledger_entries.debit_user_id', '=', 'debit_u.user_id')
            ->leftJoin('users as credit_u', 'ledger_entries.credit_user_id', '=', 'credit_u.user_id')
            ->select(
                'ledger_entries.*',
                DB::raw("CONCAT(COALESCE(debit_u.Fname, ''), ' ', COALESCE(debit_u.Lname, '')) as debit_name"),
                DB::raw("CONCAT(COALESCE(credit_u.Fname, ''), ' ', COALESCE(credit_u.Lname, '')) as credit_name")
            )
            ->orderBy('ledger_entries.entry_id', 'desc')
            ->paginate(15);

        // قائمة جميع المحافظ
        $wallets = DB::table('wallets')
            ->join('users', 'wallets.user_id', '=', 'users.user_id')
            ->select(
                'wallets.*',
                DB::raw("CONCAT(COALESCE(users.Fname, ''), ' ', COALESCE(users.Lname, '')) as name"),
                'users.email'
            )
            ->orderBy('wallets.balance', 'desc')
            ->get();

        return view('admin.accounting.index', compact(
            'totalWalletsBalance',
            'totalEarnedAll',
            'totalWithdrawnAll',
            'totalPendingPayouts',
            'entries',
            'wallets'
        ));
    }

    /**
     * عرض وتتبع طلبات سحب الأرباح
     */
    public function payouts(Request $request)
    {
        $payouts = DB::table('payout_requests')
            ->join('users', 'payout_requests.user_id', '=', 'users.user_id')
            ->leftJoin('wallets', 'users.user_id', '=', 'wallets.user_id')
            ->select(
                'payout_requests.*',
                DB::raw("CONCAT(COALESCE(users.Fname, ''), ' ', COALESCE(users.Lname, '')) as name"),
                'users.email',
                'wallets.balance as current_balance'
            )
            ->orderBy('payout_requests.payout_id', 'desc')
            ->paginate(15);

        return view('admin.accounting.payouts', compact('payouts'));
    }

    /**
     * قبول وتأكيد طلب السحب
     */
    public function approvePayout(Request $request, int $id)
    {
        $adminId = auth()->id() ?? 1;
        $notes = $request->input('admin_notes', 'تم التحويل بنجاح من مدير النظام');

        $success = $this->accountingService->approvePayout($id, $adminId, $notes);

        if ($success) {
            return redirect()->back()->with('success', 'تم تأكيد طلب السحب وتسجيل القيد المحاسبي بنجاح');
        }

        return redirect()->back()->with('error', 'فشل في معالجة طلب السحب');
    }

    /**
     * رفض طلب السحب وإعادة المبلغ للمحفظة
     */
    public function rejectPayout(Request $request, int $id)
    {
        $payout = DB::table('payout_requests')->where('payout_id', $id)->first();
        if (!$payout || $payout->status !== 'pending') {
            return redirect()->back()->with('error', 'طلب غير صالح');
        }

        DB::transaction(function () use ($payout, $request) {
            DB::table('payout_requests')->where('payout_id', $payout->payout_id)->update([
                'status' => 'rejected',
                'admin_notes' => $request->input('admin_notes', 'تم الرفض بواسطة الإدارة'),
                'processed_at' => now(),
            ]);

            // إرجاع المبلغ المعلق للرصيد الفعلي
            DB::table('wallets')->where('user_id', $payout->user_id)->decrement('pending_withdrawal', $payout->amount);
            DB::table('wallets')->where('user_id', $payout->user_id)->increment('balance', $payout->amount);
        });

        return redirect()->back()->with('success', 'تم رفض طلب السحب وإعادة المبلغ لرصيد المحفظة');
    }

    /**
     * تسجيل مصروف تشغيلي للنظام
     */
    public function storeExpense(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'expense_date' => 'required|date',
        ]);

        $adminId = auth()->id() ?? 1;

        DB::table('system_expenses')->insert([
            'category' => $request->input('category'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
            'expense_date' => $request->input('expense_date'),
            'logged_by_admin_id' => $adminId,
            'created_at' => now(),
        ]);

        // تسجيل قيد اليومية
        $this->accountingService->recordLedgerEntry(
            $adminId,
            0,
            (float) $request->input('amount'),
            'SYSTEM_EXPENSE',
            "مصروف تشغيلي: {$request->input('category')} - {$request->input('description')}",
            $adminId,
            'EXPENSE_' . time()
        );

        return redirect()->back()->with('success', 'تم تسجيل المصروف التشغيلي والقيد المحاسبي بنجاح');
    }

    /**
     * تطبيق غرامة أو خصم مالي على مستخدم / مطعم / أخصائي
     */
    public function applyPenalty(Request $request)
    {
        $request->validate([
            'target_user_id' => 'required|exists:users,user_id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string',
        ]);

        $adminId = auth()->id() ?? 1;
        $targetUserId = (int) $request->input('target_user_id');
        $amount = (float) $request->input('amount');
        $reason = $request->input('reason');

        // تسجيل القيد: خصم من محفظة الهدف وإضافتها لحساب الأدمن أو تسجيلها كـ PENALTY_DEDUCTION
        $this->accountingService->recordLedgerEntry(
            $targetUserId,
            $adminId,
            $amount,
            'PENALTY_DEDUCTION',
            "تطبيق خصم/غرامة مالية: {$reason}",
            $adminId,
            'PENALTY_' . time()
        );

        return redirect()->back()->with('success', 'تم تطبيق الغرامة المالية وتحديث المحفظة بنجاح');
    }
}
