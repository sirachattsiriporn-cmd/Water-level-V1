<?php
// api/mqtt_control.php
header('Content-Type: application/json');
// ปิด Error Report หน้าเว็บเพื่อไม่ให้ JSON พัง (แต่ Log ลงไฟล์แทนได้)
error_reporting(0); 

require("../includes/phpMQTT.php");

$server = "127.0.0.1";
$port = 1883;
$username = "";
$password = "";
$client_id = "php_gate_" . uniqid();

$response = array();
$command = "";

// ---------------------------------------------------------
// 🟢 ส่วนที่เพิ่ม: พยายามรับค่าจากหลายๆ ทาง (กันเหนียว)
// ---------------------------------------------------------

// 1. ลองรับจาก JSON (มาตรฐาน)
$raw_input = file_get_contents('php://input');
$json_input = json_decode($raw_input, true);
if (isset($json_input['command'])) {
    $command = $json_input['command'];
}

// 2. ถ้าไม่มี ลองรับจาก URL ตรงๆ (GET) -> ไว้ทดสอบพิมพ์ URL เอง
if (empty($command) && isset($_GET['command'])) {
    $command = $_GET['command'];
}

// ---------------------------------------------------------

if (empty($command)) {
    $response['status'] = false;
    $response['message'] = "No command received. (Input was empty)";
    echo json_encode($response);
    exit();
}

// เชื่อมต่อ MQTT
$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

if ($mqtt->connect(true, NULL, $username, $password)) {
    $topic = "water_monitor/gate/control";
    $msg = ($command == 'open') ? "1" : "0";
    
    $mqtt->publish($topic, $msg, 0);
    $mqtt->close();
    
    $response['status'] = true;
    $response['message'] = "Success! Sent '$msg' to MQTT";
} else {
    $response['status'] = false;
    $response['message'] = "Connect MQTT Failed. Is Mosquitto running?";
}

echo json_encode($response);
?>