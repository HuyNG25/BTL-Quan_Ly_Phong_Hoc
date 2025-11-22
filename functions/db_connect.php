<?php
// QUANLYPHONGHOC/functions/db_connect.php

function connectDB() {
    // Thay đổi thông tin kết nối CSDL của bạn tại đây
    $servername = "localhost"; 
    $username = "root"; // Tên người dùng CSDL của bạn
    $password = "03092005";     // Mật khẩu CSDL của bạn
    $dbname = "quanlyphonghoc"; // Tên database của bạn

    // Tạo kết nối
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        // Thay vì die, bạn có thể chuyển hướng đến trang lỗi
        die("Kết nối CSDL thất bại: " . $conn->connect_error);
    }
    
    // Đặt bộ ký tự thành UTF-8 cho tiếng Việt
    $conn->set_charset("utf8mb4"); 
    
    return $conn;
}

// Hàm ngắt kết nối
function closeDB($conn) {
    if ($conn instanceof mysqli) {
        $conn->close();
    }
}
?>
