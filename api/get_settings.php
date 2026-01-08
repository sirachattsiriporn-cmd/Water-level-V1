<?php
// api/get_settings.php
header('Content-Type: application/json');
require("../configs/db_connect.php");

// ดึงค่าจากแถวที่ 1 เสมอ
$sql = "SELECT start_val, stop_val, diff_val FROM system_settings WHERE id = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    echo json_encode(['start_val' => 0, 'stop_val' => 0, 'diff_val' => 0]);
}
?>