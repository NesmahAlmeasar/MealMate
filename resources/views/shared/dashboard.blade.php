@extends('layouts.admin_app')

@section('title', 'لوحة التحكم المتقدمة')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
    /* Using Global Variables from unified-design.css */
    :root {
        /* Override specifically if needed, but prefer global vars */
        --card-padding: var(--spacing-lg);
    }

    .dashboard-container {
        padding: var(--spacing-lg);
        background: var(--bg-gray);
        min-height: 100vh;
        font-size: clamp(14px, 1vw, 16px);
    }

    /* Header & Filter */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: var(--spacing-xl);
        gap: var(--spacing-md);
    }

    .dashboard-title {
        font-size: clamp(1.5rem, 2.5vw, 2rem);
        font-weight: 700;
        color: var(--olive-dark);
        margin: 0;
    }

    .filter-form {
        display: flex;
        gap: var(--spacing-sm);
        align-items: center;
        background: white;
        padding: var(--spacing-sm);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }

    .form-control-sm {
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 5px 10px;
        font-size: 14px;
        color: var(--text-dark);
    }

    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 8px 15px;
        border-radius: var(--radius-md);
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all var(--transition-fast);
        white-space: nowrap;
    }

    /* Colors */
    .btn-filter { 
        background: linear-gradient(135deg, var(--olive-medium) 0%, var(--olive-dark) 100%);
        color: white; 
        box-shadow: var(--shadow-sm);
    }
    .btn-print { 
        background: var(--bg-white); 
        color: var(--text-medium); 
        border: 1px solid var(--border-color);
    }
    .btn-print:hover { background: var(--bg-gray); color: var(--text-dark); }
    .btn-icon:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }

    /* Sections */
    /* Sections */
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: var(--spacing-lg);
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title::before {
        content: '';
        display: block;
        width: 5px;
        height: 25px;
        background: var(--olive-medium);
        border-radius: var(--radius-full);
    }

    /* Cards Grid - Global definition in unified-design.css */


    /* Charts Grid - Compact */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
    }

    .chart-card {
        background: white;
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        border: 1px solid var(--border-color);
    }
    
    .chart-container {
        position: relative;
        height: 250px; /* Fixed height for consistency */
        width: 100%;
    }

    .chart-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: var(--spacing-md);
        color: var(--text-dark);
        padding-bottom: var(--spacing-sm);
        border-bottom: 1px solid var(--bg-gray-dark);
    }

    /* Tables */
    .data-table-container {
        overflow-x: auto;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9em;
    }

    .custom-table th, .custom-table td {
        padding: 0.5rem 0.75rem;
        text-align: right;
        border-bottom: 1px solid #f1f1f1;
    }

    .custom-table th { 
        background: var(--bg-gray); 
        color: var(--text-dark); 
        font-weight: 700; 
        white-space: nowrap;
        text-align: right;
    }
    .custom-table td {
        border-bottom: 1px solid var(--border-color);
        color: var(--text-medium);
    }

    /* Print Styles */
    /* Print Styles */
    @media print {
        @page { size: landscape; margin: 10mm; } /* Optional: Landscape for better chart fit */
        body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        
        .sidebar, .navbar, .btn-print, .filter-form, .dashboard-tabs { display: none !important; }
        .dashboard-container { padding: 0; background: white; font-size: 10pt; }
        
        /* Maintain Grid Layout */
        .charts-grid, .stats-grid { 
            display: grid !important; 
            grid-template-columns: repeat(2, 1fr) !important; 
            gap: 20px !important; 
        }
        
        /* Ensure cards look right */
        .stat-card, .chart-card { 
            break-inside: avoid; 
            page-break-inside: avoid;
            box-shadow: none; 
            border: 1px solid #ccc;
            width: auto !important;
            margin-bottom: 0 !important;
        }

        .chart-container { height: 250px !important; } /* Keep height consistent */
        
        /* ONLY print the active tab */
        .tab-content { display: none !important; }
        .tab-content.active { display: block !important; }
    }

    /* Tab Navigation Styles */
    .dashboard-tabs {
        display: flex;
        gap: var(--spacing-sm);
        margin-bottom: var(--spacing-xl);
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 2px;
        overflow-x: auto;
        flex-wrap: nowrap;
    }

    .tab-btn {
        padding: 10px 20px;
        border: none;
        background: transparent;
        color: var(--text-medium);
        font-weight: 600;
        cursor: pointer;
        border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        transition: all var(--transition-fast);
        font-size: 1rem;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 3px solid transparent;
        margin-bottom: -4px; /* overlapping border */
    }

    .tab-btn:hover {
        background: var(--bg-gray);
        color: var(--olive-dark);
    }

    .tab-btn.active {
        background: white;
        color: var(--olive-dark);
        border-bottom: 3px solid var(--olive-medium);
    }

    .tab-content {
        display: none;
        animation: fadeIn 0.4s ease-in-out;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container" id="printableArea">
    
    <!-- Header & Controls -->
    <div class="dashboard-header">
       
        <div class="d-flex gap-2">
            <form action="{{ route('shared.dashboard') }}" method="GET" class="filter-form">
                <input type="date" name="date_from" class="form-control-sm" value="{{ $dateFrom }}">
                <span class="text-muted">-</span>
                <input type="date" name="date_to" class="form-control-sm" value="{{ $dateTo }}">
                <button type="submit" class="btn-icon btn-filter">🔍 تصفية</button>
                 <button onclick="window.print()" class="btn-icon btn-print">🖨️ طباعة التقرير</button>

            </form>
            

        </div>


         <div>
            <h1 class="dashboard-title">📊 لوحة التحكم</h1>
        </div>
        
    </div>

    <!-- Role-Based Navigation Tabs -->
    <div class="dashboard-tabs">
        @if(Auth::user()->hasRole('Admin'))
        <button class="tab-btn" onclick="openTab('tab-admin', this)">
            <span>👁️</span> نظرة عامة
        </button>
        @endif

        @if(Auth::user()->hasRole('Restaurant Manager'))
        <button class="tab-btn" onclick="openTab('tab-manager', this)">
            <span>🍳</span> إدارة المطعم
        </button>
        @endif

        @if(Auth::user()->hasRole('Specialist') || Auth::user()->hasRole('Nutrition Manager'))
        <button class="tab-btn" onclick="openTab('tab-specialist', this)">
            <span>🩺</span> عيادتي
        </button>
        @endif

        @if(Auth::user()->hasRole('Nutrition Manager'))
        <button class="tab-btn" onclick="openTab('tab-nutrition', this)">
            <span>🧪</span> التحليلات الصحية
        </button>
        @endif
    </div>

    {{-- =======================================================
         1. SYSTEM ADMIN DASHBOARD (Eagle Eye)
         ======================================================= --}}
    <div id="tab-admin" class="tab-content">
    @if(Auth::user()->hasRole('Admin'))
    <section class="mb-5">
        <h3 class="section-title text-primary mb-3">👁️ نظرة عامة (مدير النظام)</h3>
        
        <div class="stats-grid">
            <div class="stat-card" style="border-color: var(--green-success)">
                <div class="stat-label">💰 إجمالي الإيرادات</div>
                <div class="stat-value">{{ number_format($admin_total_revenue) }} ريال</div>
                <small class="text-success">شامل الطلبات والاستشارات</small>
            </div>
            
            <div class="stat-card" style="border-color: var(--blue-primary)">
                <div class="stat-label">👥 نمو المستخدمين</div>
                <div class="stat-value">+{{ $admin_new_users }}</div>
                <small class="text-muted"> مستخدم جديد في هذه الفترة</small>
            </div>

            <div class="stat-card" style="border-color: var(--red-accent)">
                <div class="stat-label">🔥 النشاط المباشر</div>
                <div class="stat-value">{{ $admin_active_orders + $admin_active_consultations }}</div>
                <small class="text-danger">{{ $admin_active_orders }} طلب نشط | {{ $admin_active_consultations }} استشارة</small>
            </div>

            <div class="stat-card" style="border-color: var(--text-medium)">
                <div class="stat-label">⚠️ حالة النظام</div>
                <div class="stat-value">{{ $admin_system_errors }}</div>
                <small class="text-muted">أخطاء معلقة (Failed Jobs)</small>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <h4 class="chart-title">🕒 أوقات الذروة (النظام)</h4>
                <div class="chart-container">
                    <canvas id="adminPeakHoursChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <h4 class="chart-title">🏆 أداء كبار الأخصائيين</h4>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>الأخصائي</th>
                                <th>استشارات</th>
                                <th>إيرادات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admin_specialists_performance as $spec)
                            <tr>
                                <td>{{ $spec['name'] }}</td>
                                <td>{{ $spec['consultations_count'] }}</td>
                                <td>{{ number_format($spec['revenue']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    @endif
    </div>

    {{-- =======================================================
         2. RESTAURANT MANAGER DASHBOARD
         ======================================================= --}}
    <div id="tab-manager" class="tab-content">
    @if(Auth::user()->hasRole('Restaurant Manager'))
    <section class="mb-5">
        <h3 class="section-title text-warning mb-3">🍳 إدارة المطعم والعمليات</h3>
        
        <div class="charts-grid">
            <div class="chart-card">
                <h4 class="chart-title">📈 المبيعات (يومياً)</h4>
                <div class="chart-container">
                    <canvas id="managerSalesChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <h4 class="chart-title">🥧 تحليل حالات الطلبات</h4>
                <div class="chart-container">
                    <canvas id="managerOrderTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <h4 class="chart-title">🍔 أكثر الوجبات مبيعاً</h4>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>الوجبة</th>
                            <th>الكمية المباعة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($manager_top_meals as $meal)
                        <tr>
                            <td>{{ $meal->meal->name ?? 'غير معروف' }}</td>
                            <td>{{ $meal->total_qty }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="chart-card">
                <h4 class="chart-title">⏰ ساعات ضغط الطلبات</h4>
                <div class="chart-container">
                    <canvas id="managerPeakHoursChart"></canvas>
                </div>
            </div>
        </div>
    </section>
    @endif
    </div>

    {{-- =======================================================
         3. SPECIALIST DASHBOARD
         ======================================================= --}}
    <div id="tab-specialist" class="tab-content">
    @if(Auth::user()->hasRole('Specialist') || Auth::user()->hasRole('Nutrition Manager'))
    <section class="mb-5">
        <h3 class="section-title text-info mb-3">🩺 عيادتي (الأخصائي)</h3>
        
        <div class="stats-grid">
            <div class="stat-card" style="border-color: var(--olive-medium)">
                <div class="stat-label">💬 استشارات نشطة</div>
                <div class="stat-value">{{ $specialist_active_count }}</div>
            </div>
            <div class="stat-card" style="border-color: var(--green-success)">
                <div class="stat-label">💵 الدخل الشخصي (الفترة)</div>
                <div class="stat-value">{{ number_format($specialist_revenue) }}</div>
            </div>
            <div class="stat-card" style="border-color: var(--text-medium)">
                <div class="stat-label">⚖️ متوسط BMI العملاء</div>
                <div class="stat-value">{{ $specialist_avg_bmi }}</div>
            </div>
        </div>

        <div class="chart-card">
            <h4 class="chart-title">📅 المواعيد القادمة</h4>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>المستخدم</th>
                            <th>التاريخ</th>
                            <th>الوقت</th>
                            <th>النوع</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($specialist_upcoming as $app)
                        <tr>
                            <td>{{ $app->client->Fname ?? 'عميل' }} {{ $app->client->Lname ?? '' }}</td>
                            <td>{{ $app->start_time->format('Y-m-d') }}</td>
                            <td>{{ $app->start_time->format('H:i') }}</td>
                            <td><span class="badge bg-info">{{ $app->type->name ?? 'عام' }}</span></td>
                            <td><a href="{{ route('chat.index', ['user_id' => $app->client_id]) }}" class="btn btn-sm btn-primary">محادثة</a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">لا توجد مواعيد قريبة</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    @endif
    </div>

    {{-- =======================================================
         4. NUTRITION MANAGER DASHBOARD
         ======================================================= --}}
    <div id="tab-nutrition" class="tab-content">
    @if(Auth::user()->hasRole('Nutrition Manager'))
    <section class="mb-5">
        <h3 class="section-title text-success mb-3">🧪 التحليلات الصحية (مدير التغذية)</h3>
        
        <div class="charts-grid">
            <div class="chart-card">
                <h4 class="chart-title">🦠 أكثر الأمراض المزمنة انتشاراً</h4>
                <div class="chart-container">
                    <canvas id="nutritionChronicChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <h4 class="chart-title">🤧 الحساسيات الأكثر شيوعاً</h4>
                <div class="chart-container">
                    <canvas id="nutritionAllergyChart"></canvas>
                </div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <h4 class="chart-title">💊 الأدوية الأكثر استخداماً</h4>
                <div class="chart-container">
                    <canvas id="nutritionMedicationChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h4 class="chart-title">🥗 الحميات الأكثر شعبية</h4>
                <div class="chart-container">
                    <canvas id="nutritionDietChart"></canvas>
                </div>
            </div>
        </div>
    </section>
    @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Tab Navigation Logic
    function openTab(tabId, btnElement) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Show selected tab
        const selectedTab = document.getElementById(tabId);
        if (selectedTab) {
            selectedTab.classList.add('active');
        }
        
        // Activate button
        if (btnElement) {
            btnElement.classList.add('active');
        }
    }

    // Initialize: Open first available tab
    document.addEventListener('DOMContentLoaded', function() {
        const firstBtn = document.querySelector('.tab-btn');
        if (firstBtn) {
            firstBtn.click();
        }
    });

    // Common Config
    Chart.defaults.font.family = "'Cairo', sans-serif";
    Chart.defaults.maintainAspectRatio = false; // Important for responsive containers
    
    // ---------------- ADMIN CHARTS ---------------- //
    @if(Auth::user()->hasRole('Admin'))
    const adminPeakData = {!! json_encode($admin_peak_hours) !!};
    new Chart(document.getElementById('adminPeakHoursChart'), {
        type: 'line',
        data: {
            labels: adminPeakData.map(d => d.hour + ':00'),
            datasets: [{
                label: 'عدد الطلبات',
                data: adminPeakData.map(d => d.count),
                borderColor: '#EF4444',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(239, 68, 68, 0.1)'
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
    @endif

    // ---------------- MANAGER CHARTS ---------------- //
    @if(Auth::user()->hasRole('Restaurant Manager'))
    const salesData = {!! json_encode($manager_sales_data) !!};
    new Chart(document.getElementById('managerSalesChart'), {
        type: 'bar',
        data: {
            labels: salesData.map(d => d.date),
            datasets: [{
                label: 'المبيعات (ريال)',
                data: salesData.map(d => d.total),
                backgroundColor: '#10B981'
            }]
        }
    });

    const trendsData = {!! json_encode($manager_order_trends) !!};
    new Chart(document.getElementById('managerOrderTrendsChart'), {
        type: 'doughnut',
        data: {
            labels: trendsData.map(d => d.state),
            datasets: [{
                data: trendsData.map(d => d.count),
                backgroundColor: ['#F59E0B', '#10B981', '#EF4444', '#6B7280']
            }]
        }
    });

    const managerPeakData = {!! json_encode($manager_peak_hours) !!};
    new Chart(document.getElementById('managerPeakHoursChart'), {
        type: 'bar',
        data: {
            labels: managerPeakData.map(d => d.hour + ':00'),
            datasets: [{
                label: 'ضغط الطلبات',
                data: managerPeakData.map(d => d.count),
                backgroundColor: '#F59E0B'
            }]
        }
    });
    @endif

    // ---------------- NUTRITION MANAGER CHARTS ---------------- //
    @if(Auth::user()->hasRole('Nutrition Manager'))
    const chronicData = {!! json_encode($nutrition_top_chronic) !!};
    new Chart(document.getElementById('nutritionChronicChart'), {
        type: 'pie',
        data: {
            labels: chronicData.map(d => d.chronic_diseases),
            datasets: [{
                data: chronicData.map(d => d.clients_count),
                backgroundColor: ['#EF4444', '#3B82F6', '#F59E0B', '#10B981', '#6B8E23']
            }]
        }
    });

    const allergyData = {!! json_encode($nutrition_top_allergies) !!};
    new Chart(document.getElementById('nutritionAllergyChart'), {
        type: 'pie',
        data: {
            labels: allergyData.map(d => d.allergies),
            datasets: [{
                data: allergyData.map(d => d.clients_count),
                backgroundColor: ['#EF4444', '#F59E0B', '#FCD34D', '#10B981', '#3B82F6']
            }]
        }
    });

    const medData = {!! json_encode($nutrition_top_medications) !!};
    new Chart(document.getElementById('nutritionMedicationChart'), {
        type: 'bar',
        data: {
            labels: medData.map(d => d.medical_record),
            datasets: [{
                label: 'عدد المستخدمين',
                data: medData.map(d => d.clients_count),
                backgroundColor: '#6B8E23'
            }]
        },
        options: { indexAxis: 'y' }
    });

    const dietData = {!! json_encode($nutrition_diet_popularity) !!};
    new Chart(document.getElementById('nutritionDietChart'), {
        type: 'bar',
        data: {
            labels: dietData.map(d => d.diet ? d.diet.name : 'Unknown'),
            datasets: [{
                label: 'عدد المشتركين',
                data: dietData.map(d => d.count),
                backgroundColor: '#3B82F6'
            }]
        },
        options: { indexAxis: 'y' }
    });
    @endif
</script>
@endpush
