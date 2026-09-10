@extends('layouts.admin_app')

@section('title', 'معاملات بوابة بس الإلكترونية')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h1 class="page-title" style="margin: 0;">🛒 سجل معاملات بوابة الدفع (BAS Gateway)</h1>
            <p style="color: #666; margin-top: 5px; font-size: 0.9rem;">متابعة كافة المدفوعات الإلكترونية، الاستشارات، الوجبات، وشحن المحفظة</p>
        </div>
        <div>
            <a href="{{ route('admin.accounting.index') }}" class="btn-primary" style="text-decoration: none;">
                <i class="fas fa-book me-1"></i> الانتقال للدفتر المحاسبي
            </a>
        </div>
    </div>

    <!-- Filter Bar & Table Card -->
    <div class="table-card" style="background: white; border-radius: 10px; border: 1px solid #E5E7EB; overflow: hidden;">
        <div style="padding: 15px 20px; background: #F9FAF5; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 700; color: #556B2F;">
                <i class="fas fa-credit-card me-1"></i> جميع المعاملات عبر بوابة بس
            </div>
            <form method="GET" action="{{ route('admin.payments.index') }}" style="display: flex; gap: 10px;">
                <select name="payable_type" class="form-control" style="padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;" onchange="this.form.submit()">
                    <option value="">كل الأنواع</option>
                    <option value="CART" {{ $typeFilter == 'CART' ? 'selected' : '' }}>طلبيات وجبات (CART)</option>
                    <option value="CONSULTING" {{ $typeFilter == 'CONSULTING' ? 'selected' : '' }}>استشارات (CONSULTING)</option>
                    <option value="WALLET_REFILL" {{ $typeFilter == 'WALLET_REFILL' ? 'selected' : '' }}>شحن محفظة (WALLET_REFILL)</option>
                </select>
                <select name="status" class="form-control" style="padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;" onchange="this.form.submit()">
                    <option value="">كل الحالات</option>
                    <option value="paid" {{ $statusFilter == 'paid' ? 'selected' : '' }}>مكتملة (Paid)</option>
                    <option value="pending_payment" {{ $statusFilter == 'pending_payment' ? 'selected' : '' }}>قيد الانتظار (Pending)</option>
                    <option value="failed" {{ $statusFilter == 'failed' ? 'selected' : '' }}>فاشلة (Failed)</option>
                </select>
            </form>
        </div>

        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #F3F6EE; color: #556B2F; text-align: right;">
                        <th style="padding: 12px 15px;">رقم المعاملة</th>
                        <th style="padding: 12px 15px;">العميل / المستخدم</th>
                        <th style="padding: 12px 15px;">نوع المعاملة</th>
                        <th style="padding: 12px 15px;">المبلغ</th>
                        <th style="padding: 12px 15px;">طريقة الدفع</th>
                        <th style="padding: 12px 15px;">الحالة</th>
                        <th style="padding: 12px 15px;">تاريخ الدفع</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr style="border-bottom: 1px solid #F3F4F6;">
                        <td style="padding: 12px 15px; font-weight: 700;">
                            #{{ $payment->payment_id }}
                            <br>
                            <span style="font-size: 0.75rem; color: #888; font-family: monospace;">{{ $payment->transaction_id ?? '-' }}</span>
                        </td>
                        <td style="padding: 12px 15px;">
                            <strong style="color: var(--text-dark);">{{ $payment->user_name ?? 'عميل ' . $payment->user_id }}</strong><br>
                            <span style="font-size: 0.8rem; color: #777;">{{ $payment->user_email ?? $payment->user_phone }}</span>
                        </td>
                        <td style="padding: 12px 15px;">
                            @if($payment->payable_type == 'CART')
                                <span style="background: #E0E7D1; color: #556B2F; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">🛒 وجبات #{{ $payment->payable_id }}</span>
                            @elseif($payment->payable_type == 'CONSULTING')
                                <span style="background: #DBEAFE; color: #1E40AF; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">👨‍⚕️ استشارة #{{ $payment->payable_id }}</span>
                            @else
                                <span style="background: #D1FAE5; color: #065F46; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">💳 شحن محفظة</span>
                            @endif
                        </td>
                        <td style="padding: 12px 15px; font-weight: 700; color: var(--text-dark);">{{ number_format($payment->amount, 2) }} ر.ي</td>
                        <td style="padding: 12px 15px;"><span style="background: #F3F4F6; color: #374151; font-weight: 600; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem;">{{ strtoupper($payment->payment_method ?? 'BAS') }}</span></td>
                        <td style="padding: 12px 15px;">
                            @if(($payment->status ?? '') == 'paid')
                                <span style="background: #ECFDF5; color: #047857; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">تم السداد ✅</span>
                            @elseif(($payment->status ?? '') == 'pending' || ($payment->status ?? '') == 'pending_payment')
                                <span style="background: #FEF3C7; color: #D97706; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">قيد الانتظار ⏳</span>
                            @else
                                <span style="background: #FEE2E2; color: #DC2626; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">فاشلة / ملغاة ❌</span>
                            @endif
                        </td>
                        <td style="padding: 12px 15px; color: #888; font-size: 0.85rem;">{{ $payment->created_at ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #888;">
                            لا توجد معاملات مدفوعات مسجلة حالياً
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 15px;">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
