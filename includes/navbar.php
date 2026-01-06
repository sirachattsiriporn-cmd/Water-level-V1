<?php
// หาชื่อไฟล์ปัจจุบัน (เช่น index.php หรือ history.php)
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-water"></i>
            Smart Water Monitor
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" 
                       href="index.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'history.php') ? 'active' : ''; ?>" 
                       href="history.php">
                        <i class="fas fa-history"></i> ข้อมูลย้อนหลัง
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>