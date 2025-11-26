<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>التقارير - لوحة تحكم المدير</title>
  <link rel="stylesheet" href="styles-2.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
  <style>
    /* General styles for RTL and centering */
    body {
      direction: rtl;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
      padding: 15px;
    }
    .container {
      max-width: 1200px;
      margin: 0 auto;
      min-height: 95vh;
    }
    .main-content {
      padding: 15px;
    }
    
    /* Reports Styles */
    .report-filter-bar {
      background-color: white;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      display: flex;
      gap: 15px;
      align-items: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    .report-filter-bar label {
      font-weight: 600;
      color: var(--text-dark);
    }
    .report-filter-bar select, .report-filter-bar input[type="date"] {
      padding: 8px 12px;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      font-size: 14px;
    }
    .report-filter-bar button {
      background-color: var(--blue-primary);
      color: white;
      padding: 8px 15px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }
    .report-filter-bar button:hover {
      background-color: #1D4ED8;
    }

    .report-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
    }
    
    .chart-container {
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    .chart-title {
      font-size: 18px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 15px;
      border-bottom: 1px solid var(--border-color);
      padding-bottom: 10px;
    }
    .chart-container canvas {
      max-height: 350px;
    }
    
    /* Table Report Style */
    .table-report-container {
      grid-column: 1 / -1;
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    .report-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      font-size: 13px;
    }
    .report-table th, .report-table td {
      text-align: right;
      padding: 10px;
      border-bottom: 1px solid var(--border-color);
    }
    .report-table th {
      background-color: var(--olive-very-light);
      font-weight: 600;
      color: var(--olive-dark);
    }
    .report-table tr:hover {
      background-color: #F9FAFB;
    }
    .report-table .metric-value {
      font-weight: 700;
      color: var(--blue-primary);
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar (Simplified for standalone page) -->
    <aside class="sidebar">
      <div class="logo">
        <div class="logo-icon"><i class="material-icons">dashboard</i></div>
        <div class="logo-text">لوحة تحكم المدير</div>
      </div>
      <nav class="nav-menu">
        <a href="dashboard.html" class="nav-item">
          <span class="nav-icon"><i class="material-icons">analytics</i></span>
          <span>الإحصائيات الرئيسية</span>
        </a>
        <a href="users.html" class="nav-item">
          <span class="nav-icon"><i class="material-icons">group</i></span>
          <span>إدارة المستخدمين</span>
        </a>
        <a href="specialists.html" class="nav-item">
          <span class="nav-icon"><i class="material-icons">medical_services</i></span>
          <span>إدارة الأخصائيين</span>
        </a>
        <a href="restaurants.html" class="nav-item">
          <span class="nav-icon"><i class="material-icons">restaurant</i></span>
          <span>إدارة المطاعم</span>
        </a>
        <a href="diets.html" class="nav-item">
          <span class="nav-icon"><i class="material-icons">fitness_center</i></span>
          <span>إدارة الحميات</span>
        </a>
        <a href="meals.html" class="nav-item">
          <span class="nav-icon"><i class="material-icons">lunch_dining</i></span>
          <span>إدارة الوجبات</span>
        </a>
        <a href="reports.html" class="nav-item active">
          <span class="nav-icon"><i class="material-icons">assessment</i></span>
          <span>التقارير</span>
        </a>
      </nav>
      <div class="doctor-card">
        <div class="doctor-card-image"><i class="material-icons">security</i></div>
        <div class="doctor-card-text">
          بصفتك المدير، أنت قلب النظام النابض.
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Header -->
      <header class="header">
        <div class="search-bar">
          <input type="text" placeholder="ابحث هنا...">
          <span class="search-icon"><i class="material-icons">search</i></span>
        </div>
        <div class="header-actions">
          <div class="language-toggle">
            <button class="lang-btn active" data-lang="ar">العربية</button>
            <button class="lang-btn" data-lang="en">English</button>
          </div>
          <div style="position: relative;">
            <button class="icon-button" id="notificationsBtn" title="الإشعارات">
              <i class="material-icons">notifications</i>
              <span class="notification-badge">3</span>
            </button>
            <div class="notifications-dropdown" id="notificationsDropdown" style="display: none;">
              <!-- Notifications content here -->
            </div>
          </div>
          <div class="user-profile">
            <div class="user-avatar">ن</div>
            <div class="user-name">مرحباً، نجمة</div>
          </div>
          <button class="icon-button" title="تسجيل الخروج">
            <i class="material-icons">logout</i>
          </button>
        </div>
      </header>

      <!-- Reports Page -->
      <div id="reports-page" class="page-content active">
        <h1 class="dashboard-title">التقارير الدورية وتحليل النظام</h1>
        
        <div class="report-filter-bar">
          <label for="report-type">نوع التقرير:</label>
          <select id="report-type">
            <option value="monthly">تقرير شهري</option>
            <option value="quarterly">تقرير ربع سنوي</option>
            <option value="yearly">تقرير سنوي</option>
          </select>
          
          <label for="start-date">من تاريخ:</label>
          <input type="date" id="start-date" value="2024-01-01">
          
          <label for="end-date">إلى تاريخ:</label>
          <input type="date" id="end-date" value="2024-06-30">
          
          <button onclick="generateReport()">توليد التقرير</button>
        </div>

        <div class="report-grid">
          
          <!-- Report 1: System Activity Over Time -->
          <div class="chart-container">
            <div class="chart-title">حركة النظام: العملاء الجدد والطلبات (بيانات وهمية)</div>
            <canvas id="activityChart"></canvas>
          </div>
          
          <!-- Report 2: Most Active Restaurants -->
          <div class="chart-container">
            <div class="chart-title">أكثر المطاعم تفاعلاً (بيانات وهمية)</div>
            <canvas id="restaurantActivityChart"></canvas>
          </div>
          
          <!-- Report 3: Specialist Performance -->
          <div class="chart-container">
            <div class="chart-title">أداء الأخصائيين: عدد العملاء (بيانات وهمية)</div>
            <canvas id="specialistChart"></canvas>
          </div>
          
          <!-- Report 4: Diet/Meal Approval Rate -->
          <div class="chart-container">
            <div class="chart-title">نسبة اعتماد الحميات والوجبات (بيانات وهمية)</div>
            <canvas id="approvalChart"></canvas>
          </div>

          <!-- Report 5: Detailed Table Report -->
          <div class="table-report-container">
            <div class="chart-title">ملخص إحصائي مفصل</div>
            <table class="report-table">
              <thead>
                <tr>
                  <th>المقياس</th>
                  <th>القيمة</th>
                  <th>التغير (شهر)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>إجمالي العملاء</td>
                  <td class="metric-value">550</td>
                  <td style="color: green;">+5.2%</td>
                </tr>
                <tr>
                  <td>إجمالي الطلبات</td>
                  <td class="metric-value">1500</td>
                  <td style="color: red;">-1.5%</td>
                </tr>
                <tr>
                  <td>متوسط قيمة الطلب</td>
                  <td class="metric-value">120 ريال</td>
                  <td style="color: green;">+2.0%</td>
                </tr>
                <tr>
                  <td>الحميات المعتمدة</td>
                  <td class="metric-value">15</td>
                  <td style="color: green;">+1</td>
                </tr>
              </tbody>
            </table>
          </div>
          
        </div>
      </div>
    </main>
  </div>

  <script src="app.js"></script>
  <script>
    let activityChartInstance = null;
    let restaurantActivityChartInstance = null;
    let specialistChartInstance = null;
    let approvalChartInstance = null;

    function initCharts() {
      // Destroy existing chart instances if they exist
      if (activityChartInstance) activityChartInstance.destroy();
      if (restaurantActivityChartInstance) restaurantActivityChartInstance.destroy();
      if (specialistChartInstance) specialistChartInstance.destroy();
      if (approvalChartInstance) approvalChartInstance.destroy();

      // Chart 1: System Activity Over Time (Line Chart)
      const activityCtx = document.getElementById('activityChart');
      if (activityCtx) {
        activityChartInstance = new Chart(activityCtx, {
          type: 'line',
          data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [
              {
                label: 'عملاء جدد',
                data: [10, 15, 25, 30, 28, 40],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
              },
              {
                label: 'إجمالي الطلبات',
                data: [100, 120, 150, 180, 160, 200],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
              }
            ]
          },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } }
        });
      }

      // Chart 2: Most Active Restaurants (Bar Chart)
      const restaurantActivityCtx = document.getElementById('restaurantActivityChart');
      if (restaurantActivityCtx) {
        restaurantActivityChartInstance = new Chart(restaurantActivityCtx, {
          type: 'bar',
          data: {
            labels: ['مطعم أ', 'مطعم ب', 'مطعم ج', 'مطعم د'],
            datasets: [{
              label: 'عدد الطلبات',
              data: [350, 280, 190, 150],
              backgroundColor: ['#F59E0B', '#EF4444', '#3B82F6', '#10B981'],
            }]
          },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
      }

      // Chart 3: Specialist Performance (Doughnut Chart)
      const specialistCtx = document.getElementById('specialistChart');
      if (specialistCtx) {
        specialistChartInstance = new Chart(specialistCtx, {
          type: 'doughnut',
          data: {
            labels: ['د. سارة علي', 'د. خالد ناصر', 'أخصائيون آخرون'],
            datasets: [{
              label: 'عدد العملاء',
              data: [250, 180, 120],
              backgroundColor: ['#3B82F6', '#EF4444', '#6B7280'],
              hoverOffset: 4
            }]
          },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
        });
      }

      // Chart 4: Diet/Meal Approval Rate (Pie Chart)
      const approvalCtx = document.getElementById('approvalChart');
      if (approvalCtx) {
        approvalChartInstance = new Chart(approvalCtx, {
          type: 'pie',
          data: {
            labels: ['حميات معتمدة', 'وجبات معتمدة', 'قيد المراجعة'],
            datasets: [{
              label: 'عدد',
              data: [15, 40, 5],
              backgroundColor: ['#10B981', '#3B82F6', '#F59E0B'],
              hoverOffset: 4
            }]
          },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
        });
      }
    }
    
    function generateReport() {
        alert(`جاري توليد تقرير من نوع: ${document.getElementById('report-type').value} للفترة من ${document.getElementById('start-date').value} إلى ${document.getElementById('end-date').value} (وظيفة وهمية).`);
        initCharts(); // Re-initialize charts with new (dummy) data
    }

    // Call initCharts after a slight delay to ensure canvas is rendered
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(initCharts, 200);
    });
    
    // Re-setup navigation from dashboard.html script block to ensure it works
    function setupPageNavigation() {
      document.querySelectorAll('.sidebar a').forEach(item => {
        item.addEventListener('click', function(e) {
          const targetPage = this.getAttribute('href');
          if (targetPage && targetPage !== '#') {
            // Simple navigation for this example
            window.location.href = targetPage;
          }
        });
      });
    }
    
    // Call setup functions
    setupPageNavigation();
  </script>
</body>
</html>
