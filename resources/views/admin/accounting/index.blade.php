@extends('layouts.admin_app')

@section('title', 'النظام المحاسبي والقيود اليومية')

@push('styles')
<style>
    .accounting-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .stat-card-olive {
        background: var(--olive-very-light, #F3F6EE);
        border: 1px solid var(--olive-light, #E0E7D1);
        border-radius: var(--radius-lg, 10px);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s ease;
    }

    .stat-card-olive:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-card-info h3 {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--olive-dark, #556B2F);
        margin: 5px 0 0 0;
    }

    .stat-card-info span {
        font-size: 0.85rem;
        color: var(--text-medium, #4B5563);
        font-weight: 600;
    }

    .stat-card-icon {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: white;
        color: var(--olive-medium, #6B8E23);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .tabs-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        border-bottom: 2px solid var(--border-color, #E5E7EB);
        padding-bottom: 10px;
    }

    .tab-btn {
        background: transparent;
        border: none;
        padding: 10px 20px;
        font-weight: 700;
        font-size: 14px;
        color: var(--text-light, #6B7280);
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .tab-btn.active {
        background-color: var(--olive-medium, #6B8E23);
        color: white;
    }

    .tab-content-panel {
        display: none;
    }

    .tab-content-panel.active {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h1 class="page-title" style="margin: 0;">📊 النظام المحاسبي الموحد والقيود اليومية</h1>
            <p style="color: #666; margin-top: 5px; font-size: 0.9rem;">متابعة كافة المحافظ المالية، عمولات المنصة، وقيود اليومية</p>
        </div>
        <div>
            <a href="{{ route('admin.accounting.payouts') }}" class="btn-primary" style="text-decoration: none;">
                <i class="fas fa-hand-holding-usd me-1"></i> طلبات سحب الأرباح ({{ $totalPendingPayouts }} ر.ي معلق)
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #ECFDF5; color: #065F46; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #A7F3D0;">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Cards Stats -->
    <div class="accounting-stats-grid">
        <div class="stat-card-olive">
            <div class="stat-card-info">
                <span>إجمالي الأرباح التراكمية</span>
                <h3>{{ number_format($totalEarnedAll, 2) }} <small style="font-size: 0.8rem;">ر.ي</small></h3>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>

        <div class="stat-card-olive">
            <div class="stat-card-info">
                <span>إجمالي الرصيد بالمحافظ</span>
                <h3>{{ number_format($totalWalletsBalance, 2) }} <small style="font-size: 0.8rem;">ر.ي</small></h3>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-wallet"></i>
            </div>
        </div>

        <div class="stat-card-olive">
            <div class="stat-card-info">
                <span>سحبيات قيد الانتظار</span>
                <h3 style="color: #D97706;">{{ number_format($totalPendingPayouts, 2) }} <small style="font-size: 0.8rem;">ر.ي</small></h3>
            </div>
            <div class="stat-card-icon" style="color: #D97706;">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>

        <div class="stat-card-olive">
            <div class="stat-card-info">
                <span>إجمالي المسحوبات المحولة</span>
                <h3>{{ number_format($totalWithdrawnAll, 2) }} <small style="font-size: 0.8rem;">ر.ي</small></h3>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs-bar">
        <button class="tab-btn active" onclick="switchTab('ledger-panel', this)">
            📜 قيود اليومية الموحدة (Ledger Entries)
        </button>
        <button class="tab-btn" onclick="switchTab('wallets-panel', this)">
            💼 أرصدة المحافظ المالية (All Wallets)
        </button>
    </div>

    <!-- Tab Content Panels -->
    <div id="ledger-panel" class="tab-content-panel active">
        <div class="table-card" style="background: white; border-radius: 10px; border: 1px solid #E5E7EB; overflow: hidden;">
            <div style="padding: 15px 20px; background: #F9FAF5; border-bottom: 1px solid #E5E7EB; font-weight: 700; color: #556B2F;">
                <i class="fas fa-list-alt me-1"></i> السجل العام لقيود اليومية المحاسبية
            </div>
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #F3F6EE; color: #556B2F; text-align: right;">
                        <th style="padding: 12px 15px;">رقم القيد / المرجع</th>
                        <th style="padding: 12px 15px;">نوع العملية</th>
                        <th style="padding: 12px 15px;">من حساب (مدين)</th>
                        <th style="padding: 12px 15px;">إلى حساب (دائن)</th>
                        <th style="padding: 12px 15px;">المبلغ</th>
                        <th style="padding: 12px 15px;">الوصف</th>
                        <th style="padding: 12px 15px;">التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                    <tr style="border-bottom: 1px solid #F3F4F6;">
                        <td style="padding: 12px 15px; font-weight: 700;"><code>{{ $entry->transaction_ref }}</code></td>
                        <td style="padding: 12px 15px;">
                            <span style="background: #F0F4E3; color: #556B2F; font-size: 0.8rem; padding: 4px 10px; border-radius: 6px; font-weight: 700;">
                                {{ $entry->entry_type }}
                            </span>
                        </td>
                        <td style="padding: 12px 15px; color: #DC2626; font-weight: 600;">{{ $entry->debit_name ?? 'المنصة / جهة عامة' }}</td>
                        <td style="padding: 12px 15px; color: #059669; font-weight: 600;">{{ $entry->credit_name ?? 'المنصة / جهة عامة' }}</td>
                        <td style="padding: 12px 15px; font-weight: 700;">{{ number_format($entry->amount, 2) }} ر.ي</td>
                        <td style="padding: 12px 15px; color: #666; font-size: 0.85rem;">{{ $entry->description ?? '-' }}</td>
                        <td style="padding: 12px 15px; color: #888; font-size: 0.85rem;">{{ $entry->created_at }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #888;">
                            لا توجد قيود محاسبية مسجلة حالياً
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div style="padding: 15px;">
                {{ $entries->links() }}
            </div>
        </div>
    </div>

    <div id="wallets-panel" class="tab-content-panel">
        <div class="table-card" style="background: white; border-radius: 10px; border: 1px solid #E5E7EB; overflow: hidden;">
            <div style="padding: 15px 20px; background: #F9FAF5; border-bottom: 1px solid #E5E7EB; font-weight: 700; color: #556B2F;">
                <i class="fas fa-wallet me-1"></i> أرصدة المحافظ المالية للمستخدمين والمطاعم
            </div>
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #F3F6EE; color: #556B2F; text-align: right;">
                        <th style="padding: 12px 15px;">المستخدم</th>
                        <th style="padding: 12px 15px;">البريد الإلكتروني</th>
                        <th style="padding: 12px 15px;">الرصيد المتاح</th>
                        <th style="padding: 12px 15px;">إجمالي الأرباح</th>
                        <th style="padding: 12px 15px;">المبالغ المسحوبة</th>
                        <th style="padding: 12px 15px;">سحبيات معلقة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wallets as $wallet)
                    <tr style="border-bottom: 1px solid #F3F4F6;">
                        <td style="padding: 12px 15px; font-weight: 700; color: var(--text-dark);">{{ $wallet->name }}</td>
                        <td style="padding: 12px 15px; color: #666;"><code>{{ $wallet->email }}</code></td>
                        <td style="padding: 12px 15px;">
                            <span style="background: #ECFDF5; color: #047857; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                {{ number_format($wallet->balance, 2) }} ر.ي
                            </span>
                        </td>
                        <td style="padding: 12px 15px; font-weight: 600;">{{ number_format($wallet->total_earned, 2) }} ر.ي</td>
                        <td style="padding: 12px 15px; color: #666;">{{ number_format($wallet->total_withdrawn, 2) }} ر.ي</td>
                        <td style="padding: 12px 15px;">
                            @if($wallet->pending_withdrawal > 0)
                                <span style="background: #FEF3C7; color: #D97706; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                    {{ number_format($wallet->pending_withdrawal, 2) }} ر.ي
                                </span>
                            @else
                                <span style="color: #aaa;">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #888;">لا توجد محافظ مسجلة</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function switchTab(panelId, btn) {
    document.querySelectorAll('.tab-content-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(panelId).classList.add('active');
    btn.classList.add('active');
}
</script>
@endsection
