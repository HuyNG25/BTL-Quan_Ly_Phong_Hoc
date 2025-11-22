<?php
// update_profile.php
session_start();

require_once '../../functions/db_connect.php';
require_once '../../functions/UserFunctions.php'; 

// --- Kiểm tra và Chuẩn bị ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile_admin.php');
    exit;
}

// Lấy user ID từ Session (ưu tiên) hoặc từ trường ẩn trong POST
$adminId = $_SESSION['user']['user_id'] ?? $_POST['user_id'] ?? null; 
if (!$adminId) {
    // Chuyển hướng đến trang login nếu không có ID người dùng
    header('Location: login.php'); 
    exit;
}

$userFunc = new UserFunctions();
$updateSuccess = true;
$message = [];

// --- 1. Xử lý Cập nhật Thông tin Cơ bản (Email, Fullname) ---
$email = trim($_POST['email'] ?? '');
// Lấy từ trường name="fullname" đã sửa trong form
$fullName = trim($_POST['fullname'] ?? '');
// Lấy role từ trường ẩn trong form hoặc session
$role = $_POST['role'] ?? $_SESSION['user']['role'] ?? 'admin'; 

// Chỉ cập nhật nếu có dữ liệu hợp lệ
if (!empty($email) && !empty($fullName)) {
    
    // Hàm updateUser($id, $fullname, $email, $role)
    if ($userFunc->updateUser($adminId, $fullName, $email, $role)) {
        $message[] = "Cập nhật thông tin Email và Tên thành công.";
        
        // Cập nhật Session
        $_SESSION['user']['fullname'] = $fullName;
        $_SESSION['user']['email'] = $email;

    } else {
        $message[] = "Lỗi khi cập nhật thông tin cơ bản.";
        $updateSuccess = false;
    }
}


// --- 2. Xử lý Đổi Mật khẩu (Nếu người dùng nhập) ---
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Kiểm tra xem người dùng có ý định đổi mật khẩu không
if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $message[] = "Để đổi mật khẩu, bạn phải điền đầy đủ **Mật khẩu hiện tại**, **Mật khẩu mới** và **Xác nhận mật khẩu**.";
        $updateSuccess = false;
    } elseif ($newPassword !== $confirmPassword) {
        $message[] = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
        $updateSuccess = false;
    } else {
        // Lấy email hiện tại để xác minh mật khẩu cũ
        $currentEmail = $_SESSION['user']['email'] ?? $userFunc->getUserById($adminId)['email']; 
        
        // XÁC MINH MẬT KHẨU HIỆN TẠI (Sử dụng hàm login() so sánh Plain Text)
        if ($userFunc->login($currentEmail, $currentPassword)) {
            // Mật khẩu hiện tại đúng, tiến hành đổi mật khẩu mới (Lưu Plain Text)
            if ($userFunc->changePassword($adminId, $newPassword)) {
                $message[] = "Đổi mật khẩu thành công.";
            } else {
                $message[] = "Lỗi hệ thống khi đổi mật khẩu mới.";
                $updateSuccess = false;
            }
        } else {
            // Mật khẩu hiện tại không đúng
            $message[] = "Mật khẩu hiện tại không đúng.";
            $updateSuccess = false;
        }
    }
}


// --- 3. Chuyển hướng và thông báo kết quả ---
if (count($message) > 0) {
    if ($updateSuccess) {
        $_SESSION['success_message'] = implode("<br>", $message);
    } else {
        $_SESSION['error_message'] = implode("<br>", $message);
    }
}

// Chuyển hướng người dùng trở lại trang hồ sơ
header('Location: profile_admin.php');
exit;
?>
