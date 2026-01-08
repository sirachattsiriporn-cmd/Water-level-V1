<?php
// api/mqtt_control.php
header('Content-Type: application/json');
error_reporting(0);

require("../includes/phpMQTT.php");
require("../configs/db_connect.php"); // <--- 1. เพิ่มไฟล์เชื่อมต่อฐานข้อมูล

// ตั้งค่า MQTT Broker
$server = "127.0.0.1";
$port = 1883;
$username = "";
$password = "";
$client_id = "php_dashboard_" . uniqid();

$response = array();

$type = $_POST['type'] ?? '';
$value = $_POST['value'] ?? '';

if (empty($type) || $value === '') {
    echo json_encode(['status' => false, 'message' => 'Missing type or value']);
    exit();
}

// กำหนด Topic และชื่อ Column ในฐานข้อมูล
$topic = "";
$db_column = ""; // <--- ตัวแปรสำหรับชื่อ Column

switch ($type) {
    case 'start':
        $topic = "water/control/start";
        $db_column = "start_val"; // ชื่อ column ใน DB
        break;
    case 'stop':
        $topic = "water/control/stop";
        $db_column = "stop_val"; // ชื่อ column ใน DB
        break;
    case 'diff':
        $topic = "water/control/diff";
        $db_column = "diff_val"; // ชื่อ column ใน DB
        break;
    default:
        echo json_encode(['status' => false, 'message' => 'Invalid command type']);
        exit();
}

// เชื่อมต่อ MQTT
$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

if ($mqtt->connect(true, NULL, $username, $password)) {
    // 1. ส่งค่าไป MQTT
    $mqtt->publish($topic, $value, 0); 
    $mqtt->close();
    
    // 2. บันทึกค่าล่าสุดลง Database (Update แถวที่ id=1 เสมอ)
    // ตรวจสอบความปลอดภัยข้อมูลก่อนลง DB (กัน SQL Injection)
    $safe_value = $conn->real_escape_string($value);
    
    // อัปเดตเฉพาะ Column ที่มีการส่งค่ามา
    $sql_update = "UPDATE system_settings SET $db_column = '$safe_value' WHERE id = 1";
    $conn->query($sql_update);

    $response['status'] = true;
    $response['message'] = "Sent $value to $topic and Saved to DB";
} else {
    $response['status'] = false;
    $response['message'] = "MQTT Connection Failed";
}

echo json_encode($response);
?>