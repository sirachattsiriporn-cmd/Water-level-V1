<?php
// api/mqtt_control.php
header('Content-Type: application/json');
error_reporting(0);

require("../includes/phpMQTT.php");
require("../configs/db_connect.php");

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

// =================================================================
// [ส่วนที่เพิ่มใหม่] เตรียมค่าสำหรับบันทึกลงฐานข้อมูล (แยกกับค่าที่ส่ง MQTT)
// =================================================================
$db_value = $value; // ค่าเริ่มต้นให้เท่ากับค่าที่ส่งมา

if ($type == 'close_time') {
    // ตรวจสอบว่ามีเครื่องหมาย : หรือไม่ (เช่นส่งมา "1:30")
    if (strpos($value, ':') !== false) {
        $parts = explode(':', $value);
        $min = intval($parts[0]);
        $sec = intval($parts[1]);
        
        // สูตร: (นาที x 60) + วินาที = วินาทีรวมทั้งหมด (เช่น 90)
        // บันทึกค่านี้ลงตัวแปร $db_value เพื่อเตรียมลง DB
        $db_value = ($min * 60) + $sec; 
    } 
}

// กำหนด Topic และชื่อ Column ในฐานข้อมูล
$topic = "";
$db_column = "";

switch ($type) {
    case 'start':
        $topic = "water/control/start";
        $db_column = "start_val"; 
        break;
    case 'stop':
        $topic = "water/control/stop";
        $db_column = "stop_val"; 
        break;
    case 'diff':
        $topic = "water/control/diff";
        $db_column = "diff_val"; 
        break;
    case 'close_time':
        $topic = "water/control/close_time";
        $db_column = "close_time_val"; 
        break;
    default:
        echo json_encode(['status' => false, 'message' => 'Invalid command type']);
        exit();
}

// เชื่อมต่อ MQTT
$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

if ($mqtt->connect(true, NULL, $username, $password)) {
    // 1. ส่งค่าไป MQTT 
    // (ส่ง $value เดิมที่เป็น "1:30" ไป เพื่อให้ Node-RED ทำหน้าที่แยกค่าตาม Logic เดิมของคุณ)
    $mqtt->publish($topic, $value, 0);
    $mqtt->close();

    // 2. บันทึกค่าลง Database
    // (ใช้ $db_value ที่เป็นตัวเลขวินาทีรวมแล้ว เช่น 90)
    $safe_val = $conn->real_escape_string($db_value); 

    // อัปเดตข้อมูล
    $sql_update = "UPDATE system_settings SET $db_column = '$safe_val' WHERE id = 1";
    $conn->query($sql_update);

    $response['status'] = true;
    $response['message'] = "Sent $value to MQTT and Saved $db_value to DB";
} else {
    $response['status'] = false;
    $response['message'] = "MQTT Connection Failed";
}

echo json_encode($response);
?>