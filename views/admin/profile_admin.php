<?php
// Giả định có các file admin header/footer
include 'header.php'; 

// Giả định lấy thông tin admin hiện tại từ Session
$adminId = $_SESSION['user']['user_id'] ?? null;
$adminFullname = $_SESSION['user']['fullname'] ?? 'Lệ Phi Vũ';
$adminEmail = $_SESSION['user']['email'] ?? 'V@email.com';
$adminRole = $_SESSION['user']['role'] ?? 'Quản Trị Viên';
?>

<div class="container-fluid">
    <h1 class="mt-4">👤 Thông tin Cá nhân Quản trị viên</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard_admin.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Hồ sơ</li>
    </ol>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>


    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-edit me-1"></i>
            Chỉnh sửa Hồ sơ
        </div>
        <div class="card-body">
            <form action="update_profile.php" method="POST">
                
                <input type="hidden" name="user_id" value="<?php echo $adminId; ?>">
                <input type="hidden" name="role" value="<?php echo $adminRole; ?>">
                
                <div class="mb-3">
                    <label for="fullname" class="form-label">Họ và Tên</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo $adminFullname; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $adminEmail; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="role_display" class="form-label">Quyền</label>
                    <input type="text" class="form-control" id="role_display" value="<?php echo $adminRole; ?>" readonly>
                </div>
                
                <hr>

                <h3>Đổi Mật khẩu</h3>
                <div class="mb-3">
                    <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                    <input type="password" class="form-control" id="current_password" name="current_password">
                </div>
                
                <div class="mb-3">
                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                    <input type="password" class="form-control" id="new_password" name="new_password">
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Xác nhận Mật khẩu mới</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Lưu thay đổi</button>
            </form>
        </div>
    </div>
</div>

<?php 
include 'footer.php'; 
?>
