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

  <div class="header-section d-flex justify-content-between align-items-center px-4 py-3">
    <div class="d-flex align-items-center">
        <div>
            <h1><i class="fas fa-tachometer-alt"></i> Water Level Dashboard</h1>
            <p class="mb-0">ระบบติดตามและแจ้งเตือนระดับน้ำแบบเรียลไทม์</p>
        </div>    
    </div>

    <button class="btn btn-warning btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#settingsModal">
        <i class="fas fa-cogs"></i> ตั้งค่าเกณฑ์ควบคุม
    </button>
  </div>

  <div class="container-fluid px-4">
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
        <div id="current_date_display" class="h6 mb-0 fw-bold text-secondary">Loading...</div>
        <div class="stat-value" id="time-stat" style="font-size: 1.2rem;">--</div>
        <div class="stat-label">อัปเดตล่าสุด</div>
      </div>
    </div>

    <div class="gauges-grid">
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

    <div class="row mt-4 mb-4">
      <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 15px;">
          <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            
            <div>
                <h5 class="mb-1 fw-bold"><i class="fas fa-dungeon text-secondary"></i> สถานะประตูน้ำ (Water Gate)</h5>
                <p class="text-muted mb-0 small">ติดตามสถานะ Q1 (เปิด) และ Q2 (ปิด) จาก LOGO! PLC</p>
            </div>

            <div class="d-flex align-items-center gap-4">
                
                <div class="text-center px-3 py-2 rounded" id="status-box-open" style="background: #f0f0f0; min-width: 120px; transition: all 0.3s;">
                    <i class="fas fa-arrow-up mb-1" id="icon-q1" style="font-size: 1.5rem; color: #ccc;"></i>
                    <div class="fw-bold small text-muted" id="text-q1">Open (Q1)</div>
                </div>

                <div class="text-center px-3 py-2 rounded" id="status-box-close" style="background: #f0f0f0; min-width: 120px; transition: all 0.3s;">
                    <i class="fas fa-arrow-down mb-1" id="icon-q2" style="font-size: 1.5rem; color: #ccc;"></i>
                    <div class="fw-bold small text-muted" id="text-q2">Close (Q2)</div>
                </div>

                <div class="ms-3 text-end d-none d-sm-block">
                    <div class="small text-muted">สถานะปัจจุบัน</div>
                    <div class="h4 fw-bold mb-0" id="gate-main-status">
                        <span class="badge bg-secondary">Standby</span>
                    </div>
                </div>

            </div>
          </div>
        </div>
      </div>
    </div>
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

    <div class="text-center mb-4">
      <div class="update-time">
        <i class="fas fa-sync-alt"></i>
        <span>อัปเดตล่าสุด: <strong id="time_show">กำลังโหลด...</strong></span>
      </div>
    </div>
  </div>

  <div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title"><i class="fas fa-sliders-h"></i> ตั้งค่าเกณฑ์ควบคุม (LOGO! PLC)</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4" style="background: #f8f9fa;">
          
          <div class="mb-4">
            <label class="form-label fw-bold text-success"><i class="fas fa-play-circle"></i> ระดับน้ำถนนเริ่มทำงาน (Start)</label>
            <div class="input-group">
              <input type="number" id="input_start" class="form-control" placeholder="VW4 (เช่น 500)">
              <button class="btn btn-success" onclick="sendControl('start')">บันทึก</button>
            </div>
            <div class="form-text text-muted">ค่าระดับน้ำที่สั่งให้ประตูน้ำเริ่มเปิดทำงาน</div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-bold text-danger"><i class="fas fa-stop-circle"></i> ระดับน้ำถนนหยุดทำงาน (Stop)</label>
            <div class="input-group">
              <input type="number" id="input_stop" class="form-control" placeholder="VW6 (เช่น 450)">
              <button class="btn btn-danger" onclick="sendControl('stop')">บันทึก</button>
            </div>
            <div class="form-text text-muted">ค่าระดับน้ำที่สั่งให้ประตูน้ำหยุดทำงาน</div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-bold text-primary"><i class="fas fa-exchange-alt"></i> เกณฑ์ผลต่างระดับน้ำ (Diff)</label>
            <div class="input-group">
              <input type="number" id="input_diff" class="form-control" placeholder="VW8 (เช่น 10)">
              <button class="btn btn-primary" onclick="sendControl('diff')">บันทึก</button>
            </div>
            <div class="form-text text-muted">ต้องมีความต่างมากกว่าค่านี้ ประตูถึงจะทำงาน</div>
          </div>

          <div class="mb-2">
            <label class="form-label fw-bold text-dark"><i class="fas fa-clock"></i> เวลาปิดประตูน้ำ (Close Time)</label>
            
            <div class="input-group">
                <input type="number" id="input_min" class="form-control text-center" placeholder="นาที" min="0">
                
                <span class="input-group-text bg-light fw-bold">:</span>
                
                <input type="number" id="input_sec" class="form-control text-center" placeholder="วินาที" min="0" max="59">
                
                <button class="btn btn-dark" onclick="sendControl('close_time')">บันทึก</button>
            </div>
            
            <div class="form-text text-muted">ระบุเวลา (นาที : วินาที) ที่ต้องการปิดประตู</div>
          </div>
          </div>
      </div>
    </div>
  </div>

  <script src="js/script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>