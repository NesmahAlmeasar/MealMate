<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentAdminController extends Controller
{
    /**
     * عرض سجل كافة معاملات بوابة الدفع BAS الإلكترونية
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status');
        $typeFilter = $request->query('payable_type');

        $query = DB::table('payments')
            ->leftJoin('users', 'payments.user_id', '=', 'users.user_id')
            ->select(
                'payments.*',
                DB::raw("CONCAT(COALESCE(users.Fname, ''), ' ', COALESCE(users.Lname, '')) as user_name"),
                'users.email as user_email',
                'users.phone as user_phone'
            );

        if ($statusFilter) {
            $query->where('payments.status', $statusFilter);
        }

        if ($typeFilter) {
            $query->where('payments.payable_type', $typeFilter);
        }

        $payments = $query->orderBy('payments.payment_id', 'desc')->paginate(15);

        // إحصائيات المعاملات
        $totalPaidAmount = DB::table('payments')->where('status', 'paid')->sum('amount');
        $totalPendingAmount = DB::table('payments')->whereIn('status', ['pending', 'pending_payment'])->sum('amount');
        $totalFailedAmount = DB::table('payments')->where('status', 'failed')->sum('amount');
        $totalCount = DB::table('payments')->count();

        return view('admin.payments.index', compact(
            'payments',
            'totalPaidAmount',
            'totalPendingAmount',
            'totalFailedAmount',
            'totalCount',
            'statusFilter',
            'typeFilter'
        ));
    }
}
