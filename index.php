<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Level Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/justgage/1.4.0/justgage.min.js"></script>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/gauge.css">

    <style>
        .control-btn {
            border: none;
            border-radius: 15px;
            padding: 20px;
            width: 100%;
            max-width: 250px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-open {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
        }
        .btn-open:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(46, 204, 113, 0.4);
            filter: brightness(1.1);
        }
        .btn-open:active { transform: translateY(1px); }

        .btn-close {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            
        }
        .btn-close:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(231, 76, 60, 0.4);
            filter: brightness(1.1);
        }
        .btn-close:active { transform: translateY(1px); }

        .control-section {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="header-section">
        <h1><i class="fas fa-tachometer-alt"></i> Water Level Dashboard</h1>
        <p>ระบบติดตามและแจ้งเตือนระดับน้ำแบบเรียลไทม์</p>
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
                <div class="stat-value" id="time-stat" style="font-size: 1.2rem;">--</div>
                <div class="stat-label">อัปเดตล่าสุด</div>
            </div>
        </div>

        <div class="gauges-grid mb-4">
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

        <div class="control-section mb-4">
            <div class="card-header-custom mb-3">
                <i class="fas fa-gamepad"></i>
                <span>ควบคุมประตูระบายน้ำ (Gate Control)</span>
                <div class="ms-auto">
                    <span id="mqtt_status_text" class="badge bg-secondary">Ready</span>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <button class="control-btn btn-open" onclick="sendMqttCommand('open')">
                            <i class="fas fa-door-open fa-2x"></i>
                            เปิดประตู (OPEN)
                        </button>

                        <button class="control-btn btn-close" onclick="sendMqttCommand('close')">
                            <i class="fas fa-door-closed fa-2x"></i>
                            ปิดประตู (CLOSE)
                        </button>
                    </div>
                    <p class="text-muted mt-3 mb-0 small"><i class="fas fa-wifi"></i> สั่งงานผ่านระบบ MQTT</p>
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

    <script src="js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // 🟢 จุดที่เพิ่ม: ฟังก์ชันส่งคำสั่ง MQTT
        function sendMqttCommand(action) {
            const statusBadge = document.getElementById('mqtt_status_text');
            
            // เปลี่ยนสถานะเป็นกำลังส่ง
            statusBadge.innerText = "Sending...";
            statusBadge.className = "badge bg-warning text-dark";

            // 🟢 จุดที่แก้ไข: 
            // เปลี่ยนจาก fetch(..., { method: 'POST', ... }) 
            // มาเป็น fetch ที่แนบตัวแปรไปกับ URL เลย (?command=...)
            fetch('api/mqtt_control.php?command=' + action)
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    statusBadge.innerText = "Success (" + action + ")";
                    statusBadge.className = "badge bg-success";
                    
                    // สั่งปิดอัตโนมัติ (จำลอง Push Button)
                    if(action === 'open') {
                        setTimeout(() => sendMqttCommand('close'), 1000);
                    } else {
                        // คืนค่า Ready หลังจากสักพัก
                        setTimeout(() => {
                            statusBadge.innerText = "Ready";
                            statusBadge.className = "badge bg-secondary";
                        }, 2000);
                    }
                } else {
                    statusBadge.innerText = "Failed";
                    statusBadge.className = "badge bg-danger";
                    alert("Error: " + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                statusBadge.innerText = "Error";
                statusBadge.className = "badge bg-danger";
            });
        }
    </script>
</body>
</html>