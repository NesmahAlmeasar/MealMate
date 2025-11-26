@extends('layouts.admin_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'Dashboard')

{{-- 2. إضافة ملف الـ CSS الخاص بهذه الصفحة --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/styles-2.css') }}">
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')
    <div>
        <h1 class="dashboard-title" data-i18n="patientsAdherence">Patients' Adherence to Diets</h1>

        <div class="chart-container">
            <div class="chart-title" data-i18n="patientsAdherence">Patients' Adherence to Diets</div>
            <div style="position: relative; height: 300px; width: 100%;"> {{-- تم زيادة الارتفاع --}}
                <canvas id="adherenceChart"></canvas>
            </div>
        </div>

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label" data-i18n="totalOrder">Total Order</div>
                <div class="metric-chart red">81%</div>
                <div class="metric-value">81%</div>
            </div>

            <div class="metric-card">
                <div class="metric-label" data-i18n="customerGrowth">Customer Growth</div>
                <div class="metric-chart green">22%</div>
                <div class="metric-value">22%</div>
            </div>

            <div class="metric-card">
                <div class="metric-label" data-i18n="totalRevenue">Total Revenue</div>
                <div class="metric-chart blue">62%</div>
                <div class="metric-value">62%</div>
            </div>
        </div>
    </div>
@endsection


{{-- 4. إضافة الـ JS الخاص بهذه الصفحة (الرسم البياني) --}}
@push('scripts')
    {{-- إضافة مكتبة الرسوم البيانية --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    {{-- الكود المضمّن الخاص بالرسم البياني --}}
    <script>
        (function() { // تغليف الكود لمنع التعارض
            let adherenceChartInstance = null;

            function initChart() {
                const ctx = document.getElementById('adherenceChart');
                if (ctx && !adherenceChartInstance) {
                    
                    // !! ملاحظة: قمتُ بتغيير الألوان لتناسب الخلفية الفاتحة
                    const tickColor = 'rgba(0, 0, 0, 0.7)'; // كان أبيض
                    const gridColor = 'rgba(0, 0, 0, 0.1)'; // كان أبيض
                    const legendColor = '#333'; // كان أبيض

                    adherenceChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                            datasets: [
                                {
                                    label: '2023',
                                    data: [30, 40, 35, 50, 45, 60, 55, 70, 65, 75, 80, 85],
                                    borderColor: '#3B82F6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: '2024',
                                    data: [35, 45, 40, 55, 50, 65, 60, 75, 70, 80, 85, 90],
                                    borderColor: '#EF4444',
                                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { intersect: false, mode: 'index' },
                            plugins: {
                                legend: {
                                    labels: { color: legendColor, font: { size: 11 } }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.parsed.y + '%';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    ticks: { color: tickColor, font: { size: 10 }, callback: value => value + '%' },
                                    grid: { color: gridColor, drawBorder: false }
                                },
                                x: {
                                    ticks: { color: tickColor, font: { size: 10 } },
                                    grid: { color: gridColor, drawBorder: false }
                                }
                            }
                        }
                    });
                }
            }

            // تشغيل الرسم البياني
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initChart, 200);
            });
        })();
    </script>
@endpush