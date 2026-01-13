let myChart;

// ==================== Get Water Level Color Class ====================
function getWaterLevelClass(value) {
    if (value < 0) return 'level-critical'; // ติดลบ
    if (value < 20) return 'level-safe';     // 0-19: เขียวอ่อน (ปลอดภัย)
    if (value < 40) return 'level-low';      // 20-39: เขียว (ต่ำ)
    if (value < 60) return 'level-medium';   // 40-59: เหลือง (ปานกลาง)
    if (value < 80) return 'level-high';     // 60-79: ส้ม (สูง)
    return 'level-danger';                   // 80-100: แดง (อันตราย)
}

// ==================== Get Status Info ====================
function getStatusInfo(value) {
    if (value < 0) {
        return { badge: 'failed', dot: 'failed', ring: 'ring-failed', icon: 'fa-times-circle', text: 'Failed' };
    }
    if (value < 40) {
        return { badge: 'safe', dot: 'safe', ring: 'ring-safe', icon: 'fa-check-circle', text: 'Safe' };
    }
    if (value < 70) {
        return { badge: 'warning', dot: 'warning', ring: 'ring-warning', icon: 'fa-exclamation-triangle', text: 'Warning' };
    }
    return { badge: 'danger', dot: 'danger', ring: 'ring-danger', icon: 'fa-exclamation-circle', text: 'Danger' };
}

// ==================== Update Gauge Water Level ====================
function updateGauge(waterId, ringId, value, maxValue = 100) {
    const waterElement = document.getElementById(waterId);
    const ringElement = document.getElementById(ringId);
    
    if (!waterElement) return;
    
    let percentage;
    if (value < 0) {
        percentage = 0;
    } else {
        percentage = Math.min(100, (value / maxValue) * 100);
    }
    
    waterElement.style.height = percentage + '%';
    
    const levelClass = getWaterLevelClass(value);
    waterElement.className = 'gauge-water ' + levelClass;
    
    const statusInfo = getStatusInfo(value);
    if (ringElement) {
        ringElement.className = 'gauge-ring ' + statusInfo.ring;
    }
}

// ==================== Update Gauge Value and Status ====================
function updateGaugeStatus(valueId, statusId, dotId, value) {
    const valueElement = document.getElementById(valueId);
    const statusElement = document.getElementById(statusId);
    const dotElement = document.getElementById(dotId);
    
    if (valueElement) {
        valueElement.textContent = parseFloat(value).toFixed(1);
    }
    
    const statusInfo = getStatusInfo(value);
    
    if (statusElement) {
        statusElement.className = 'status-badge ' + statusInfo.badge;
        statusElement.innerHTML = `<i class="fas ${statusInfo.icon}"></i> ${statusInfo.text}`;
    }
    
    if (dotElement) {
        dotElement.className = 'status-dot ' + statusInfo.dot;
    }
}

// ==================== ฟังก์ชันอัปเดตสถานะประตูน้ำ (Q1/Q2) ====================
function updateGateStatus(q1, q2) {
    const boxOpen = document.getElementById('status-box-open');
    const iconQ1 = document.getElementById('icon-q1');
    const textQ1 = document.getElementById('text-q1');

    const boxClose = document.getElementById('status-box-close');
    const iconQ2 = document.getElementById('icon-q2');
    const textQ2 = document.getElementById('text-q2');

    const mainStatus = document.getElementById('gate-main-status');

    // 1. รีเซ็ตสถานะเป็นค่าเริ่มต้น
    if(boxOpen) boxOpen.style.background = "#f0f0f0";
    if(iconQ1) iconQ1.style.color = "#ccc";
    if(textQ1) textQ1.className = "fw-bold small text-muted";

    if(boxClose) boxClose.style.background = "#f0f0f0";
    if(iconQ2) iconQ2.style.color = "#ccc";
    if(textQ2) textQ2.className = "fw-bold small text-muted";
    
    // 2. ตรวจสอบสถานะ
    if (q1 == 1) {
        if(boxOpen) boxOpen.style.background = "#dcfce7";
        if(iconQ1) iconQ1.style.color = "#16a34a";
        if(textQ1) textQ1.className = "fw-bold small text-success";
        
        if(mainStatus) mainStatus.innerHTML = '<span class="badge bg-success"><i class="fas fa-spinner fa-spin"></i> Opening...</span>';
    } 
    else if (q2 == 1) {
        if(boxClose) boxClose.style.background = "#fee2e2";
        if(iconQ2) iconQ2.style.color = "#dc2626";
        if(textQ2) textQ2.className = "fw-bold small text-danger";
        
        if(mainStatus) mainStatus.innerHTML = '<span class="badge bg-danger"><i class="fas fa-spinner fa-spin"></i> Closing...</span>';
    } 
    else {
        if(mainStatus) mainStatus.innerHTML = '<span class="badge bg-secondary">Standby</span>';
    }
}

// ==================== Initialize Chart ====================
function initChart() {
    const ctx = document.getElementById('waterChart').getContext('2d');
    
    const gradientRoad = ctx.createLinearGradient(0, 0, 0, 400);
    gradientRoad.addColorStop(0, 'rgba(239, 68, 68, 0.5)');
    gradientRoad.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

    const gradientCanal = ctx.createLinearGradient(0, 0, 0, 400);
    gradientCanal.addColorStop(0, 'rgba(16, 185, 129, 0.5)');
    gradientCanal.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'ระดับน้ำถนน (Road)',
                    data: [],
                    borderColor: '#ef4444',
                    backgroundColor: gradientRoad,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'ระดับน้ำคลอง (Canal)',
                    data: [],
                    borderColor: '#10b981',
                    backgroundColor: gradientCanal,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            label += context.parsed.y.toFixed(1) + ' cm';
                            const value = context.parsed.y;
                            const statusInfo = getStatusInfo(value);
                            label += ' (' + statusInfo.text + ')';
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: { min: 0, max: 100 },
                x: { grid: { display: false } }
            },
            animation: { duration: 750, easing: 'easeInOutQuart' }
        }
    });

    loadHistoryData();
}

// ==================== Load History Data ====================
function loadHistoryData() {
    fetch('api/fetch_history.php')
        .then(response => response.json())
        .then(data => {
            if(data.length > 0){
                data.forEach(item => {
                    const timeOnly = item.log_time.split(' ')[1]; 
                    myChart.data.labels.push(timeOnly);
                    myChart.data.datasets[0].data.push(item.road_val);
                    myChart.data.datasets[1].data.push(item.canal_val);
                });
                myChart.update();
            }
        })
        .catch(err => console.error('Error loading history:', err));
}

// ==================== Update Data Realtime ====================
function updateData() {
    fetch('api/fetch_data.php?t=' + Date.now())
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                const roadVal = parseFloat(data.road_val);
                const canalVal = parseFloat(data.canal_val);

                // อัปเดตเวลา
                document.getElementById('time_show').innerText = data.log_time;
                const timeOnly = data.log_time.split(' ')[1];
                document.getElementById('time-stat').innerText = timeOnly;

                // อัปเดต Quick Stats
                document.getElementById('road-stat').innerText = roadVal.toFixed(1);
                document.getElementById('canal-stat').innerText = canalVal.toFixed(1);

                // อัปเดต Gauges
                updateGauge('roadWater', 'roadRing', roadVal);
                updateGaugeStatus('roadValue', 'roadStatus', 'road-dot', roadVal);

                updateGauge('canalWater', 'canalRing', canalVal);
                updateGaugeStatus('canalValue', 'canalStatus', 'canal-dot', canalVal);
                
                // อัปเดตสถานะประตูน้ำ
                const q1 = parseInt(data.q1_status || 0);
                const q2 = parseInt(data.q2_status || 0);
                updateGateStatus(q1, q2);

                // อัปเดต Chart
                if (myChart) {
                    const lastLabel = myChart.data.labels[myChart.data.labels.length - 1];
                    
                    if (lastLabel !== timeOnly) {
                        myChart.data.labels.push(timeOnly);
                        myChart.data.datasets[0].data.push(roadVal);
                        myChart.data.datasets[1].data.push(canalVal);

                        if (myChart.data.labels.length > 30) { 
                            myChart.data.labels.shift();
                            myChart.data.datasets[0].data.shift();
                            myChart.data.datasets[1].data.shift();
                        }
                        myChart.update();
                    }
                }
            }
        })
        .catch(err => console.error('Error updating data:', err));
}

// ==================== Control Function (Start/Stop/Diff) ====================
// ==================== Control Function (Unified) ====================
function sendControl(type) {
    let valueToSend = "";
    let alertMessage = "";

    // ----------------------------------------------------
    // กรณีที่ 1: เป็นการตั้งค่าเวลา (Close Time) ที่มี 2 ช่อง
    // ----------------------------------------------------
    if (type === 'close_time') {
        let min = document.getElementById('input_min').value;
        let sec = document.getElementById('input_sec').value;

        // ถ้าค่าว่าง ให้ถือเป็น 0
        if (min === "") min = "0";
        if (sec === "") sec = "0";

        // รวมร่างเป็นรูปแบบ "นาที:วินาที"
        valueToSend = min + ":" + sec;
        alertMessage = `เวลาปิดประตู: ${min} นาที ${sec} วินาที`;
    } 
    // ----------------------------------------------------
    // กรณีที่ 2: เป็นการตั้งค่าทั่วไป (Start, Stop, Diff) ที่มี 1 ช่อง
    // ----------------------------------------------------
    else {
        const inputId = 'input_' + type;
        const inputElement = document.getElementById(inputId);

        if (!inputElement) {
            console.error("Input not found: " + inputId);
            return;
        }

        valueToSend = inputElement.value;

        if (valueToSend === "") {
            alert("กรุณากรอกตัวเลขก่อนบันทึก");
            return;
        }
        
        alertMessage = `ค่า ${type}: ${valueToSend}`;
    }

    // ----------------------------------------------------
    // ส่วนส่งข้อมูล (ใช้ร่วมกัน)
    // ----------------------------------------------------
    const btn = event.target;
    // ใช้ closest('button') เผื่อกรณี user กดไปโดนไอคอนข้างในปุ่ม
    const targetBtn = btn.closest('button') || btn; 
    
    const originalHTML = targetBtn.innerHTML;
    targetBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    targetBtn.disabled = true;

    fetch('api/mqtt_control.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `type=${type}&value=${valueToSend}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            alert('บันทึกสำเร็จ! (' + alertMessage + ')');
            // ถ้าอยากให้โหลดค่าใหม่มาโชว์ทันที
            // loadCurrentSettings(); 
        } else {
            alert('เกิดข้อผิดพลาด: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('ไม่สามารถเชื่อมต่อกับ Server ได้');
    })
    .finally(() => {
        targetBtn.innerHTML = originalHTML;
        targetBtn.disabled = false;
    });
}
// ==================== Realtime Clock Function ====================
function updateRealtimeClock() {
    const now = new Date();
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const dateString = now.toLocaleDateString('th-TH', options);
    
    const dateElement = document.getElementById('current_date_display');
    if (dateElement) {
        dateElement.innerText = dateString;
    }
}

// ==================== [อัปเดต] ดึงค่า Setting ล่าสุดมาโชว์ ====================
function loadCurrentSettings() {
    fetch('api/get_settings.php')
        .then(response => response.json())
        .then(data => {
            // เช็ค ID ให้ตรงกับหน้า HTML
            const inputStart = document.getElementById('input_start'); 
            if (inputStart) inputStart.value = data.start_val;
            
            const inputStop = document.getElementById('input_stop'); 
            if (inputStop) inputStop.value = data.stop_val;
            
            const inputDiff = document.getElementById('input_diff'); 
            if (inputDiff) inputDiff.value = data.diff_val;

            // [ส่วนที่แก้เพิ่ม] สำหรับ Close Time (แปลงวินาทีรวมจาก DB -> นาที/วินาที)
            if (data.close_time_val !== undefined && data.close_time_val !== null) {
                let totalSec = parseInt(data.close_time_val);
                
                if (!isNaN(totalSec)) {
                    // คำนวณกลับ
                    let minutes = Math.floor(totalSec / 60); // หารเอาจำนวนเต็ม (นาที)
                    let seconds = totalSec % 60;             // เศษที่เหลือ (วินาที)

                    // ใส่ค่าลง 2 ช่องใหม่
                    const inputMin = document.getElementById('input_min');
                    const inputSec = document.getElementById('input_sec');

                    if (inputMin) inputMin.value = minutes;
                    if (inputSec) inputSec.value = seconds;
                }
            }
        })
        .catch(err => console.error('Error loading settings:', err));
}

// ==================== Initialize Application ====================
document.addEventListener('DOMContentLoaded', () => {
    initChart();  
    updateData(); 
    updateRealtimeClock();
    
    loadCurrentSettings(); 
    
    setInterval(updateData, 1000); 
    setInterval(updateRealtimeClock, 1000); 
});