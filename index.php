<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Water Level Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/gauge.css">
</head>
<body>
  <?php include 'includes/navbar.php'; ?>

  <!-- Header -->
  <div class="header-section">
    <h1><i class="fas fa-tachometer-alt"></i> Water Level Dashboard</h1>
    <p>ระบบติดตามและแจ้งเตือนระดับน้ำแบบเรียลไทม์</p>
  </div>

  <div class="container-fluid px-4">
    <!-- Quick Stats -->
    <div class="quick-stats">
      <div class="stat-card">
        <div class="stat-icon road">
          <i class="fas fa-road"></i>
        </div>
        <div class="stat-value" id="road-stat">--</div>
        <div class="stat-label">ระดับน้ำถนน (cm)</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon canal">
          <i class="fas fa-water"></i>
        </div>
        <div class="stat-value" id="canal-stat">--</div>
        <div class="stat-label">ระดับน้ำคลอง (cm)</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon time">
          <i class="fas fa-clock"></i>
        </div>
        <div class="stat-value" id="time-stat" style="font-size: 1.2rem;">--</div>
        <div class="stat-label">อัปเดตล่าสุด</div>
      </div>
    </div>

    <!-- Custom Gauges -->
    <div class="gauges-grid">
      <!-- Road Water Level -->
      <div class="gauge-card">
        <div class="gauge-header">
          <div class="gauge-title">
            <span class="gauge-icon">🚗</span>
            Road Water Level
          </div>
          <div class="status-dot waiting" id="road-dot"></div>
        </div>
        
        <div class="gauge-container">
          <div class="gauge-circle">
            <div class="gauge-water level-safe" id="roadWater"></div>
          </div>
          <div class="gauge-ring ring-safe" id="roadRing"></div>
          <div class="gauge-value">
            <div class="gauge-number" id="roadValue">--</div>
            <div class="gauge-unit">cm</div>
          </div>
        </div>

        <div class="status-badge waiting" id="roadStatus">
          <i class="fas fa-spinner fa-spin"></i>
          Waiting...
        </div>
      </div>

      <!-- Canal Water Level -->
      <div class="gauge-card">
        <div class="gauge-header">
          <div class="gauge-title">
            <span class="gauge-icon">🌊</span>
            Canal Water Level
          </div>
          <div class="status-dot waiting" id="canal-dot"></div>
        </div>
        
        <div class="gauge-container">
          <div class="gauge-circle">
            <div class="gauge-water level-safe" id="canalWater"></div>
          </div>
          <div class="gauge-ring ring-safe" id="canalRing"></div>
          <div class="gauge-value">
            <div class="gauge-number" id="canalValue">--</div>
            <div class="gauge-unit">cm</div>
          </div>
        </div>

        <div class="status-badge waiting" id="canalStatus">
          <i class="fas fa-spinner fa-spin"></i>
          Waiting...
        </div>
      </div>
    </div>

    <!-- Chart -->
    <div class="glass-card mb-4">
      <div class="card-header-custom">
        <i class="fas fa-chart-line"></i>
        <span>กราฟแนวโน้มระดับน้ำ (Trend Analysis)</span>
        <div class="ms-auto">
          <a href="history.php" class="btn btn-light btn-sm">
            <i class="fas fa-database"></i> ดูประวัติเต็ม
          </a>
        </div>
      </div>
      <div class="card-body-custom">
        <div class="chart-container">
          <canvas id="waterChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Update Time -->
    <div class="text-center mb-4">
      <div class="update-time">
        <i class="fas fa-sync-alt"></i>
        <span>อัปเดตล่าสุด: <strong id="time_show">กำลังโหลด...</strong></span>
      </div>
    </div>
  </div>

  <script src="js/script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>