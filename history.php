<?php
require_once 'configs/db_connect.php';

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// SQL ดึงข้อมูลรวมถึง q1_status และ q2_status (ใช้ * ก็ได้เพราะเพิ่ม col แล้ว)
$sql = "SELECT * FROM log_levels 
        WHERE DATE(log_time) BETWEEN '$start_date' AND '$end_date' 
        ORDER BY log_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/history-style.css">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="container-fluid px-4 py-4">
        <div class="header-section">
            <h1><i class="fas fa-history"></i> ประวัติข้อมูลระดับน้ำ</h1>
            <p>ดูและวิเคราะห์ข้อมูลย้อนหลังตามช่วงเวลาที่ต้องการ</p>
        </div>

        <div class="glass-card">
            <div class="card-header-custom">
                <i class="fas fa-filter"></i>
                <span>ตัวกรองข้อมูล</span>
            </div>
            <div class="card-body-custom">
                
                <form method="GET" class="row g-3 mb-4 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-calendar-alt"></i> ตั้งแต่วันที่
                        </label>
                        <input type="date" name="start_date" class="form-control form-control-lg" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-calendar-check"></i> ถึงวันที่
                        </label>
                        <input type="date" name="end_date" class="form-control form-control-lg" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search"></i> ค้นหา
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <div class="table-responsive">
                    <table id="historyTable" class="table table-hover w-100 text-center align-middle">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-clock"></i> Timestamp</th>
                                <th><i class="fas fa-road"></i> Road (cm)</th>
                                <th><i class="fas fa-signal"></i> Status</th>
                                <th><i class="fas fa-water"></i> Canal (cm)</th>
                                <th><i class="fas fa-signal"></i> Status</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['log_time']; ?></td>
                                        
                                        <td class="fw-bold text-primary"><?php echo number_format($row['road_val'], 1); ?></td>
                                        <td>
                                            <?php 
                                                if($row['road_val'] < 0) {
                                                    echo '<span class="status-badge fail"><i class="fas fa-times-circle"></i> Fail</span>';
                                                } else {
                                                    echo '<span class="status-badge reading"><i class="fas fa-check-circle"></i> Reading</span>';
                                                }
                                            ?>
                                        </td>

                                        <td class="fw-bold text-success"><?php echo number_format($row['canal_val'], 1); ?></td>
                                        <td>
                                            <?php 
                                                if($row['canal_val'] < 0) {
                                                    echo '<span class="status-badge fail"><i class="fas fa-times-circle"></i> Fail</span>';
                                                } else {
                                                    echo '<span class="status-badge reading"><i class="fas fa-check-circle"></i> Reading</span>';
                                                }
                                            ?>
                                        </td>

                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#historyTable').DataTable({
                "order": [[ 0, "desc" ]],
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "dom": "<'row mb-3'<'col-md-6'B><'col-md-6'f>>" +
                       "<'row'<'col-sm-12'tr>>" +
                       "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                
                "buttons": [
                    { 
                        extend: 'excel', 
                        text: '<i class="fas fa-file-excel"></i> Excel', 
                        className: 'btn btn-success btn-sm me-2', 
                        title: 'Water Levels Report' 
                    },
                    { 
                        extend: 'csv', 
                        text: '<i class="fas fa-file-csv"></i> CSV', 
                        className: 'btn btn-info btn-sm text-white me-2', 
                        title: 'water_levels_data' 
                    },
                    { 
                        extend: 'print', 
                        text: '<i class="fas fa-print"></i> Print', 
                        className: 'btn btn-dark btn-sm' 
                    }
                ],
                "language": {
                    "search": "<i class='fas fa-search'></i> ค้นหา:",
                    "lengthMenu": "แสดง _MENU_ รายการ",
                    "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    "paginate": { 
                        "first": "หน้าแรก", 
                        "last": "หน้าสุดท้าย", 
                        "next": "ถัดไป", 
                        "previous": "ก่อนหน้า" 
                    },
                    "emptyTable": "ไม่พบข้อมูลในช่วงเวลานี้"
                }
            });
        });
    </script>

</body>
</html>