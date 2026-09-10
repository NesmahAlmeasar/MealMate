@extends('layouts.admin_app')

@section('title', 'طلبات سحب الأرباح')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h1 class="page-title" style="margin: 0;">💳 طلبات سحب الأرباح للمطاعم والأخصائيين</h1>
            <p style="color: #666; margin-top: 5px; font-size: 0.9rem;">مراجعة وتحويل المبالغ المستحقة وإقرار القيود المحاسبية</p>
        </div>
        <div>
            <a href="{{ route('admin.accounting.index') }}" class="btn-secondary" style="text-decoration: none;">
                <i class="fas fa-arrow-right me-1"></i> العودة للدفتر المحاسبي
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #ECFDF5; color: #065F46; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #A7F3D0;">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="background: #FEE2E2; color: #991B1B; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #FCA5A5;">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="table-card" style="background: white; border-radius: 10px; border: 1px solid #E5E7EB; overflow: hidden;">
        <div style="padding: 15px 20px; background: #F9FAF5; border-bottom: 1px solid #E5E7EB; font-weight: 700; color: #556B2F;">
            <i class="fas fa-list-check me-1"></i> جميع طلبات سحب الأرباح
        </div>
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #F3F6EE; color: #556B2F; text-align: right;">
                        <th style="padding: 12px 15px;">رقم الطلب</th>
                        <th style="padding: 12px 15px;">صاحب الطلب</th>
                        <th style="padding: 12px 15px;">المبلغ المطلوب</th>
                        <th style="padding: 12px 15px;">البنك / الوسيلة</th>
                        <th style="padding: 12px 15px;">رقم الحساب / الآيبان</th>
                        <th style="padding: 12px 15px;">الحالة</th>
                        <th style="padding: 12px 15px;">تاريخ الطلب</th>
                        <th style="padding: 12px 15px; text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payouts as $payout)
                    <tr style="border-bottom: 1px solid #F3F4F6;">
                        <td style="padding: 12px 15px; font-weight: 700;">#{{ $payout->payout_id }}</td>
                        <td style="padding: 12px 15px;">
                            <strong style="color: var(--text-dark);">{{ $payout->name }}</strong><br>
                            <span style="font-size: 0.8rem; color: #777;">{{ $payout->email }}</span>
                        </td>
                        <td style="padding: 12px 15px;">
                            <span style="font-weight: 700; color: #556B2F; font-size: 1rem;">
                                {{ number_format($payout->amount, 2) }} ر.ي
                            </span>
                        </td>
                        <td style="padding: 12px 15px;">{{ $payout->bank_name ?? 'غير محدد' }}</td>
                        <td style="padding: 12px 15px;"><code>{{ $payout->account_number ?? '-' }}</code></td>
                        <td style="padding: 12px 15px;">
                            @if($payout->status == 'pending')
                                <span style="background: #FEF3C7; color: #D97706; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">
                                    قيد المراجعة ⏳
                                </span>
                            @elseif($payout->status == 'completed')
                                <span style="background: #ECFDF5; color: #047857; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">
                                    تم التأكيد والتحويل ✅
                                </span>
                            @else
                                <span style="background: #FEE2E2; color: #DC2626; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">
                                    مرفوض ❌
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px 15px; color: #888; font-size: 0.85rem;">{{ $payout->requested_at }}</td>
                        <td style="padding: 12px 15px; text-align: center;">
                            @if($payout->status == 'pending')
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <form action="{{ route('admin.accounting.payouts.approve', $payout->payout_id) }}" method="POST" onsubmit="return confirm('تأكيد تحويل المبلغ ودفع السحب؟');" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn" style="background: #10B981; color: white; padding: 6px 12px; font-size: 0.8rem; border-radius: 6px;">
                                        <i class="fas fa-check"></i> موافقة وتأكيد
                                    </button>
                                </form>
                                <form action="{{ route('admin.accounting.payouts.reject', $payout->payout_id) }}" method="POST" onsubmit="return confirm('تأكيد رفض طلب السحب؟');" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn" style="background: #EF4444; color: white; padding: 6px 12px; font-size: 0.8rem; border-radius: 6px;">
                                        <i class="fas fa-times"></i> رفض
                                    </button>
                                </form>
                            </div>
                            @else
                                <span style="color: #777; font-size: 0.85rem;">{{ $payout->admin_notes ?? 'مكتمل' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #888;">
                            لا توجد طلبات سحب أرباح حالياً
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 15px;">
            {{ $payouts->links() }}
        </div>
    </div>
</div>
@endsection
