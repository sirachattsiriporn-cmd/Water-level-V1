<?php
// api/fetch_history.php
header('Content-Type: application/json; charset=UTF-8');
require_once '../configs/db_connect.php';

// ดึงข้อมูล 20 รายการล่าสุด
$sql = "SELECT road_val, canal_val, log_time FROM log_levels ORDER BY id DESC LIMIT 20";
$result = $conn->query($sql);

$data = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    // ข้อมูลมันมาแบบ ใหม่ -> เก่า (DESC) แต่กราฟต้องวาดจาก เก่า -> ใหม่ (ซ้ายไปขวา)
    // เลยต้องกลับด้าน array
    $data = array_reverse($data);
}

echo json_encode($data);
$conn->close();
?>