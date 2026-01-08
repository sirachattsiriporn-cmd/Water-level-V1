<?php
// api/fetch_history.php
header('Content-Type: application/json; charset=UTF-8');
require_once '../configs/db_connect.php';

// ดึงข้อมูล 20 รายการล่าสุด (เพิ่ม q1_status, q2_status)
$sql = "SELECT road_val, canal_val, log_time, q1_status, q2_status FROM log_levels ORDER BY id DESC LIMIT 20";
$result = $conn->query($sql);

$data = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    // กลับด้าน array เพื่อให้กราฟวาดจาก ซ้าย(เก่า) ไป ขวา(ใหม่)
    $data = array_reverse($data);
}

echo json_encode($data);
$conn->close();
?>