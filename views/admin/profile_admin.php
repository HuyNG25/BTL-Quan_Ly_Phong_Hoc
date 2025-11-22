<?php
include 'header.php'; 
require_once('../../functions/db_connect.php');

$adminId = $_SESSION['user']['user_id'] ?? null;
$adminRole = $_SESSION['user']['role'] ?? 'Quản Trị Viên';

// Lấy thông tin age và address nếu là admin
$adminAge = '';
$adminAddress = '';
if ($adminRole === 'admin' || $adminRole === 'Quản Trị Viên') {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT age, address FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $stmt->bind_result($adminAge, $adminAddress);
    $stmt->fetch();
    $stmt->close();
    closeDB($conn);
}

// Các thông tin cơ bản
$adminFullname = $_SESSION['user']['fullname'] ?? 'Lệ Phi Vũ';
$adminEmail = $_SESSION['user']['email'] ?? 'V@email.com';
?>

<!DOCTYPE html>

<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hồ sơ Quản trị viên</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .profile-container {
        max-width: 900px;
        width: 100%;
        margin: 30px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        overflow: hidden;
        padding: 30px;
    }
    .profile-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .profile-header h1 {
        font-weight: 700;
        color: #34495e;
    }
    .card {
        border-radius: 10px;
    }
    .card-header {
        font-weight: 600;
        font-size: 1.1rem;
    }
    .card p {
        font-size: 0.95rem;
        margin-bottom: 8px;
    }
    .btn-primary, .btn-secondary {
        border-radius: 50px;
        padding: 8px 20px;
    }
    @media(max-width: 768px){
        .profile-container{
            padding: 20px;
        }
    }
</style>
</head>
<body>

<div class="profile-container">
<div class="profile-header">
    <h1><i class="fas fa-user-circle me-2"></i>Hồ sơ Quản trị viên</h1>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white"><i class="fas fa-id-card me-2"></i>Thông tin cá nhân</div>
            <div class="card-body">
                <p><strong>Họ và Tên:</strong> <?= htmlspecialchars($adminFullname) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($adminEmail) ?></p>
                <?php if ($adminRole === 'admin' || $adminRole === 'Quản Trị Viên'): ?>
                    <p><strong>Tuổi:</strong> <?= htmlspecialchars($adminAge) ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($adminAddress) ?></p>
                <?php endif; ?>
                <p><strong>Quyền:</strong> <?= htmlspecialchars($adminRole) ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white"><i class="fas fa-edit me-2"></i>Chỉnh sửa hồ sơ</div>
            <div class="card-body">
                <form action="update_profile.php" method="POST">
                    <input type="hidden" name="user_id" value="<?= $adminId ?>">
                    <input type="hidden" name="role" value="<?= $adminRole ?>">

                    <div class="mb-3">
                        <label class="form-label">Họ và Tên</label>
                        <input type="text" class="form-control" name="fullname" value="<?= htmlspecialchars($adminFullname) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($adminEmail) ?>" required>
                    </div>

                    <?php if ($adminRole === 'admin' || $adminRole === 'Quản Trị Viên'): ?>
                        <div class="mb-3">
                            <label class="form-label">Tuổi</label>
                            <input type="number" class="form-control" name="age" value="<?= htmlspecialchars($adminAge) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($adminAddress) ?>">
                        </div>
                    <?php endif; ?>

                    <hr>
                    <h6>Đổi Mật khẩu</h6>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" class="form-control" name="current_password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" name="new_password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" name="confirm_password">
                    </div>

                    <button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Lưu thay đổi</button>
                    <a href="dashboard_admin.php" class="btn btn-secondary"><i class="fas fa-home me-1"></i>Trang chủ</a>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
