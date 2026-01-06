<?php
// Security: ป้องกันการเรียกไฟล์นี้โดยตรง (Direct Access Prevention)
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    http_response_code(403);
    die('Forbidden: Access Denied');
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "water_management";

$conn = new mysqli($host, $user, $pass, $db);

// ตั้งค่าภาษาไทยและ Timezone
$conn->set_charset("utf8");
date_default_timezone_set('Asia/Bangkok');

if ($conn->connect_error) {
    // กรณี Error ให้ตายเงียบๆ ไม่ต้องโชว์ error code ให้ User เห็น (เพื่อความปลอดภัย)
    die("Connection Error"); 
}
?>