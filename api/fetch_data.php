<?php
// --- ส่วนที่ 1: สั่ง Browser ห้ามจำ Cache (เพิ่มใหม่) ---
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// --- ส่วนที่ 2: Headers เดิมของคุณ ---
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff'); 

// --- ส่วนที่ 3: เชื่อมต่อฐานข้อมูล ---
require_once '../configs/db_connect.php';

// ดึงข้อมูลล่าสุด 1 แถว
$sql = "SELECT road_val, canal_val, log_time FROM log_levels ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

$response = array();

if ($result && $result->num_rows > 0) {
    $response = $result->fetch_assoc();
    $response['status'] = true;
} else {
    $response['status'] = false;
    $response['message'] = 'Waiting for data...';
}

echo json_encode($response);
$conn->close();
?>