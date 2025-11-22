<?php
// views/lecturer/profile.php
session_start();
require_once('../../functions/db_connect.php');
require_once('../../functions/LecturerFunctions.php');

// 1. Kiểm tra vai trò và đăng nhập
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'giangvien') {
    header('Location: ../../login.php');
    exit();
}

$conn = connectDB();
$lecturer_id = $_SESSION['user']['user_id'];
$user_info = null;

// SỬA LỖI FATAL: BỎ CỘT 'username' vì nó không tồn tại. Dùng 'email' làm định danh.
$sql = "SELECT user_id, fullname, email, phone, role FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();
$user_info = $result->fetch_assoc();
$stmt->close();

closeDB($conn);

// Kiểm tra nếu không tìm thấy user (lỗi session/CSDL)
if (!$user_info) {
    $_SESSION['error_message'] = "Không tìm thấy thông tin tài khoản.";
    header('Location: dashboard_lecturer.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>👤 Thông tin Tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .profile-card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="fw-bold mb-5" style="color: #0047a0;">Thông tin Tài khoản Giảng viên</h2>

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            
            <?php 
            // Vẫn giữ lại phần hiển thị thông báo thành công/lỗi
            if (isset($_SESSION['success_message'])) { 
                echo '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>' . $_SESSION['success_message'] . '</div>'; 
                unset($_SESSION['success_message']); 
            }
            if (isset($_SESSION['error_message'])) { 
                echo '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>' . $_SESSION['error_message'] . '</div>'; 
                unset($_SESSION['error_message']); 
            }
            ?>
            
            <div class="card profile-card">
                <div class="card-header bg-primary text-white rounded-top-3">
                    <h4 class="mb-0"><i class="fas fa-user me-2"></i> Hồ sơ Cá nhân</h4>
                </div>
                <div class="card-body p-4">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên đăng nhập (Email):</label>
                        <p class="form-control-static"><?= htmlspecialchars($user_info['email']) ?></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Họ và Tên:</label>
                        <p class="form-control-static"><?= htmlspecialchars($user_info['fullname']) ?></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Liên hệ:</label>
                        <p class="form-control-static"><?= htmlspecialchars($user_info['email']) ?></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Số điện thoại:</label>
                        <p class="form-control-static"><?= htmlspecialchars($user_info['phone'] ?? 'Chưa cập nhật') ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Vai trò:</label>
                        <p class="form-control-static text-capitalize"><?= htmlspecialchars($user_info['role']) ?></p>
                    </div>

                    <hr>
                    
                    <div class="d-grid">
                        <a href="dashboard_lecturer.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i> Quay lại Dashboard
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>