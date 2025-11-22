<?php
// Tệp này giả định nằm trong views/admin/
include 'header.php'; // Sử dụng header đã cung cấp
?>

<div class="container-fluid">
    <h1 class="mt-4">📝 Soạn Thông báo Mới</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="notifications_admin.php">Thông báo</a></li>
        <li class="breadcrumb-item active">Soạn mới</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-bullhorn me-1"></i>
            Nội dung Thông báo
        </div>
        <div class="card-body">
            <form action="../../handles/handle_notification.php" method="POST">
                <input type="hidden" name="add_noti" value="1">

                <div class="mb-3">
                    <label for="title" class="form-label">Tiêu đề Thông báo</label>
                    <input type="text" class="form-control" id="title" name="title" required placeholder="Ví dụ: Bảo trì hệ thống vào 22h tối nay">
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Nội dung Chi tiết (Message)</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required placeholder="Viết nội dung mô tả chi tiết tại đây..."></textarea>
                </div>
                
                <p class="text-muted small">Thông báo này sẽ được gửi đến tất cả người dùng.</p>

                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Gửi Thông báo</button>
                <a href="notifications_admin.php" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<?php 
include 'footer.php'; 
?>