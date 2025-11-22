<?php
session_start();
require_once('../../functions/db_connect.php');
require_once('../../functions/LecturerFunctions.php');

// Kiểm tra vai trò
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'giangvien') {
    header('Location: ../../login.php');
    exit();
}

$conn = connectDB();
$lecturer_id = $_SESSION['user']['user_id'];

// Lấy thông tin giảng viên (có thêm age và address)
$sql = "SELECT user_id, fullname, email, phone, role, age, address FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$user_info = $stmt->get_result()->fetch_assoc();
$stmt->close();
closeDB($conn);

// Nếu không tìm thấy
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
<title>👤 Hồ sơ Giảng viên</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
body { 
    background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.profile-card { 
    border-radius: 20px; 
    box-shadow: 0 15px 40px rgba(0,0,0,0.15); 
    transition: transform 0.3s, box-shadow 0.3s; 
    background: rgba(255,255,255,0.95);
}
.profile-card:hover { 
    transform: translateY(-8px); 
    box-shadow: 0 25px 60px rgba(0,0,0,0.2);
}
.profile-avatar { 
    width: 120px; height: 120px; border-radius: 50%; object-fit: cover; 
    border: 4px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.2); 
    margin-top: -60px;
}
.label-icon { width: 25px; }
</style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php 
            if (isset($_SESSION['success_message'])) { 
                echo '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>' . $_SESSION['success_message'] . '</div>'; 
                unset($_SESSION['success_message']); 
            }
            if (isset($_SESSION['error_message'])) { 
                echo '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>' . $_SESSION['error_message'] . '</div>'; 
                unset($_SESSION['error_message']); 
            }
            ?>
            <div class="card profile-card p-4 position-relative">
                <div class="text-center">
                    <img src="https://i.pravatar.cc/150?u=<?= $lecturer_id ?>" alt="Avatar" class="profile-avatar">
                    <h3 class="mt-3 fw-bold"><?= htmlspecialchars($user_info['fullname']) ?></h3>
                    <p class="text-muted text-uppercase"><?= htmlspecialchars($user_info['role']) ?></p>
                </div>
                <hr>
                <div class="row text-start mt-3">
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <i class="fas fa-envelope me-2 label-icon text-primary"></i>
                        <span><?= htmlspecialchars($user_info['email']) ?></span>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <i class="fas fa-phone me-2 label-icon text-success"></i>
                        <span><?= htmlspecialchars($user_info['phone'] ?? 'Chưa cập nhật') ?></span>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <i class="fas fa-id-card me-2 label-icon text-warning"></i>
                        <span>Mã Giảng viên: <?= $user_info['user_id'] ?></span>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <i class="fas fa-birthday-cake me-2 label-icon text-danger"></i>
                        <span>Tuổi: <?= htmlspecialchars($user_info['age'] ?? 'Chưa cập nhật') ?></span>
                    </div>
                    <div class="col-12 mb-3 d-flex align-items-center">
                        <i class="fas fa-map-marker-alt me-2 label-icon text-info"></i>
                        <span>Địa chỉ: <?= htmlspecialchars($user_info['address'] ?? 'Chưa cập nhật') ?></span>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="dashboard_lecturer.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i> Quay lại trang chủ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
