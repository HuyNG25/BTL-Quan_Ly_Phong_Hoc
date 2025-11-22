<?php
// Tệp này nằm trong views/admin/
include 'header.php';
?>

<div class="container-fluid mt-4">
    <h1 class="text-center mb-4" style="color:#fff;">📝 Soạn Thông báo Mới</h1>

<ol class="breadcrumb mb-4" style="background: rgba(255,255,255,0.1); border-radius:50px; padding:10px 15px;">
    <li class="breadcrumb-item"><a href="index.php" class="text-white">Trang chủ</a></li>
    <li class="breadcrumb-item"><a href="notifications_admin.php" class="text-white">Thông báo</a></li>
    <li class="breadcrumb-item active text-white">Soạn mới</li>
</ol>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white" style="font-weight:600; font-size:1.1rem;">
        <i class="fas fa-bullhorn me-1"></i> Nội dung Thông báo
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

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-1"></i> Gửi Thông báo</button>
                <a href="notifications_admin.php" class="btn btn-secondary"><i class="fas fa-times me-1"></i> Hủy</a>
            </div>
        </form>
    </div>
</div>
```

</div>

<style>
    body {
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .form-control {
        border-radius: 50px;
    }
    .btn {
        border-radius: 50px;
    }
    .breadcrumb a {
        text-decoration: none;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>
