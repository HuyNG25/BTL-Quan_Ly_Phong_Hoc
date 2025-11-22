<?php
session_start();
require_once __DIR__ . '/../functions/UserFunctions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Chuyển hướng nếu không phải phương thức POST
    header("Location: ../login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

$userFn = new UserFunctions();
$user = $userFn->login($email, $password);

if ($user) {
    // Lưu thông tin cần thiết vào session (không lưu mật khẩu)
    $_SESSION['user'] = [
        'user_id' => $user['user_id'],
        'fullname' => $user['fullname'],
        'email' => $user['email'],
        'role' => $user['role']
    ];

    // SỬA ĐỔI: Chuyển hướng dựa trên vai trò
    if ($user['role'] === 'admin') {
        // Đường dẫn cho Admin
        header("Location: ../views/admin/dashboard_admin.php");
        
    } elseif ($user['role'] === 'giangvien') { // ĐÃ SỬA THÀNH 'giangvien' (Tiếng Việt không dấu)
        // Đường dẫn cho Giảng viên
        header("Location: ../views/lecturer/dashboard_lecturer.php");
        
    } else {
        // Dành cho các vai trò khác
        // Giả định các vai trò khác cũng dùng đường dẫn này
        header("Location: ../../views/admin/schedule.php");
    }
    
    exit;
} else {
    // Đăng nhập thất bại
    $_SESSION['error'] = 'Sai email hoặc mật khẩu. Vui lòng thử lại.';
    header('Location: ../login.php'); 
    exit;
}
?>
