<?php
session_start();

// Nếu đã đăng nhập → chuyển về dashboard phù hợp
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: views/dashboard_admin.php");
    } else {
        header("Location: views/schedule.php");
    }
    exit();
}

// Nếu chưa đăng nhập → chuyển đến trang login
header("Location: login.php");
exit();
?>
