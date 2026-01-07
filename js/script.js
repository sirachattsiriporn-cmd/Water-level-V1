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
        return {
            badge: 'failed',
            dot: 'failed',
            ring: 'ring-failed',
            icon: 'fa-times-circle',
            text: 'Failed'
        };
    }
    if (value < 40) {
        return {
            badge: 'safe',
            dot: 'safe',
            ring: 'ring-safe',
            icon: 'fa-check-circle',
            text: 'Safe'
        };
    }
    if (value < 70) {
        return {
            badge: 'warning',
            dot: 'warning',
            ring: 'ring-warning',
            icon: 'fa-exclamation-triangle',
            text: 'Warning'
        };
    }
    return {
        badge: 'danger',
        dot: 'danger',
        ring: 'ring-danger',
        icon: 'fa-exclamation-circle',
        text: 'Danger'
    };
}

// ==================== Update Gauge Water Level ====================
function updateGauge(waterId, ringId, value, maxValue = 100) {
    const waterElement = document.getElementById(waterId);
    const ringElement = document.getElementById(ringId);
    
    if (!waterElement) return;
    
    // คำนวณเปอร์เซ็นต์
    let percentage;
    if (value < 0) {
        percentage = 0;
    } else {
        percentage = Math.min(100, (value / maxValue) * 100);
    }
    
    // อัปเดตความสูงของน้ำ
    waterElement.style.height = percentage + '%';
    
    // เปลี่ยนสีของน้ำตามระดับ
    const levelClass = getWaterLevelClass(value);
    waterElement.className = 'gauge-water ' + levelClass;
    
    // เปลี่ยนสีของ ring
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
    
    // อัปเดตค่าตัวเลข
    if (valueElement) {
        valueElement.textContent = parseFloat(value).toFixed(1);
    }
    
    // ดึงข้อมูลสถานะ
    const statusInfo = getStatusInfo(value);
    
    // อัปเดต Status Badge
    if (statusElement) {
        statusElement.className = 'status-badge ' + statusInfo.badge;
        statusElement.innerHTML = `<i class="fas ${statusInfo.icon}"></i> ${statusInfo.text}`;
    }
    
    // อัปเดต Status Dot
    if (dotElement) {
        dotElement.className = 'status-dot ' + statusInfo.dot;
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
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 13,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y.toFixed(1) + ' cm';
                            
                            // เพิ่มสถานะในแต่ละจุด
                            const value = context.parsed.y;
                            const statusInfo = getStatusInfo(value);
                            label += ' (' + statusInfo.text + ')';
                            
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        stepSize: 10,
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            },
            animation: {
                duration: 750,
                easing: 'easeInOutQuart'
            }
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

                // อัปเดต Custom Gauge - Road (พร้อมเปลี่ยนสี)
                updateGauge('roadWater', 'roadRing', roadVal);
                updateGaugeStatus('roadValue', 'roadStatus', 'road-dot', roadVal);

                // อัปเดต Custom Gauge - Canal (พร้อมเปลี่ยนสี)
                updateGauge('canalWater', 'canalRing', canalVal);
                updateGaugeStatus('canalValue', 'canalStatus', 'canal-dot', canalVal);

                // อัปเดต Chart
                if (myChart) {
                    const lastLabel = myChart.data.labels[myChart.data.labels.length - 1];
                    
                    if (lastLabel !== timeOnly) {
                        myChart.data.labels.push(timeOnly);
                        myChart.data.datasets[0].data.push(roadVal);
                        myChart.data.datasets[1].data.push(canalVal);

                        // เก็บข้อมูลแค่ 30 จุดล่าสุด
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

// ==================== Control Function (NEW: เพิ่มใหม่ส่วนนี้) ====================
function sendControl(type) {
    // ดึงค่าจากช่อง Input ตามประเภทที่กด
    const inputId = 'input_' + type;
    const value = document.getElementById(inputId).value;

    // ตรวจสอบความถูกต้อง
    if (value === "") {
        alert("กรุณากรอกตัวเลขก่อนบันทึก");
        return;
    }

    // แสดงสถานะกำลังส่ง (Optional)
    const btn = event.target;
    const originalText = btn.innerText;
    btn.innerText = "Sending...";
    btn.disabled = true;

    // ส่งข้อมูลไป API
    fetch('api/mqtt_control.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `type=${type}&value=${value}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            alert('บันทึกค่าสำเร็จ! (' + type + ': ' + value + ')');
            // document.getElementById(inputId).value = ''; // ล้างค่าถ้าต้องการ
        } else {
            alert('เกิดข้อผิดพลาด: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('ไม่สามารถเชื่อมต่อกับ Server ได้');
    })
    .finally(() => {
        btn.innerText = originalText;
        btn.disabled = false;
    });
}

// ==================== Initialize Application ====================
document.addEventListener('DOMContentLoaded', () => {
    initChart();  
    updateData(); // เรียกครั้งแรกทันที
    
    // ตั้งเวลาอัปเดตทุก 1 วินาที (สำหรับ Realtime)
    setInterval(updateData, 1000); 
});