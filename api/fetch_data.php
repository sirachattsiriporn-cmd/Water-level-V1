<?php
// --- ส่วนที่ 1: สั่ง Browser ห้ามจำ Cache ---
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// --- ส่วนที่ 2: Headers เดิม ---
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff'); 

// --- ส่วนที่ 3: เชื่อมต่อฐานข้อมูล ---
require_once '../configs/db_connect.php';

// แก้ไข SQL: เพิ่มการดึงค่า q1_status และ q2_status
$sql = "SELECT road_val, canal_val, log_time, q1_status, q2_status FROM log_levels ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

$response = array();

if ($result && $result->num_rows > 0) {
    $response = $result->fetch_assoc();
    
    // แปลงค่าสถานะให้เป็น Integer (เพื่อให้ JavaScript เช็คได้ง่ายๆ ว่าเป็น 1 หรือ 0)
    // ถ้าใน Database เป็น NULL หรือไม่มีข้อมูล ให้ใส่ค่า 0 ไว้ก่อน
    $response['q1_status'] = isset($response['q1_status']) ? (int)$response['q1_status'] : 0;
    $response['q2_status'] = isset($response['q2_status']) ? (int)$response['q2_status'] : 0;

    $response['status'] = true;
} else {
    $response['status'] = false;
    $response['message'] = 'Waiting for data...';
}

echo json_encode($response);
$conn->close();
?>