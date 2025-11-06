<?php
// ===============================
// 🔗 KẾT NỐI DATABASE - quanlyphonghoc
// ===============================

function connectDB() {
    // Cấu hình kết nối
    $servername = "localhost";
    $username = "root";
    $password = "03092005";
    $dbname = "quanlyphonghoc";

    // Tạo kết nối
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("❌ Kết nối CSDL thất bại: " . $conn->connect_error);
    }

    // Đặt charset UTF-8 để tránh lỗi tiếng Việt
    $conn->set_charset("utf8mb4");

    return $conn;
}
?>
