<?php
// api/mqtt_control.php
header('Content-Type: application/json');
error_reporting(0); // ปิด Error หน้าเว็บ

require("../includes/phpMQTT.php");

// ตั้งค่า MQTT Broker
$server = "127.0.0.1"; // หรือ IP ของ Node-RED
$port = 1883;
$username = ""; // ใส่ username ถ้ามี
$password = ""; // ใส่ password ถ้ามี
$client_id = "php_dashboard_" . uniqid();

$response = array();

// รับค่าจาก AJAX (POST Method)
$type = $_POST['type'] ?? '';
$value = $_POST['value'] ?? '';

// ตรวจสอบว่ามีข้อมูลส่งมาครบถ้วนหรือไม่
if (empty($type) || $value === '') {
    echo json_encode(['status' => false, 'message' => 'Missing type or value']);
    exit();
}

// กำหนด Topic ให้ตรงกับ Node-RED ที่ตั้งไว้
$topic = "";
switch ($type) {
    case 'start':
        $topic = "water/control/start"; // ส่งไป VW4
        break;
    case 'stop':
        $topic = "water/control/stop";  // ส่งไป VW6
        break;
    case 'diff':
        $topic = "water/control/diff";  // ส่งไป VW8
        break;
    default:
        echo json_encode(['status' => false, 'message' => 'Invalid command type']);
        exit();
}

// เชื่อมต่อและส่งข้อมูล
$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

if ($mqtt->connect(true, NULL, $username, $password)) {
    // publish(topic, message, qos)
    $mqtt->publish($topic, $value, 0); 
    $mqtt->close();
    
    $response['status'] = true;
    $response['message'] = "Sent $value to $topic";
} else {
    $response['status'] = false;
    $response['message'] = "MQTT Connection Failed";
}

echo json_encode($response);
?>