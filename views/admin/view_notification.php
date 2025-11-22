<?php
// views/admin/view_notification.php
include 'header.php';
require_once '../../functions/NotificationFunctions.php';

$notiFn = new NotificationFunctions();

// Lấy user_id hiện tại
$user_id = $_SESSION['user']['user_id'] ?? 0;

// Lấy ID thông báo từ query string
$noti_id = $_GET['id'] ?? null;
if (!$noti_id) {
    header('Location: notifications_admin.php');
    exit;
}

// Đánh dấu đã đọc
$notiFn->markAsRead($user_id, $noti_id);

// Lấy thông tin chi tiết thông báo
$notification = $notiFn->getNotificationById($noti_id);
if (!$notification) {
    header('Location: notifications_admin.php?msg=not_found');
    exit;
}

// Lấy tên người tạo
$creatorName = $notiFn->getCreatorFullname($notification['sender_id']);
?>

<div class="container-fluid mt-4">
    <h1 class="mb-4">📄 Xem Thông báo</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard_admin.php">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="notifications_admin.php">Thông báo</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white" style="font-weight:600;">
            <?= htmlspecialchars($notification['title']) ?>
        </div>
        <div class="card-body">
            <p class="text-muted mb-2">
                <strong>Người gửi:</strong> <?= htmlspecialchars($creatorName) ?><br>
                <strong>Thời gian:</strong> <?= date('d/m/Y H:i', strtotime($notification['created_at'])) ?>
            </p>
            <hr>
            <p><?= nl2br(htmlspecialchars($notification['message'])) ?></p>

            <div class="mt-4">
                <a href="notifications_admin.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
                <a href="../../handles/handle_notification.php?delete_id=<?= $notification['noti_id'] ?>" 
                   class="btn btn-danger"
                   onclick="return confirm('Bạn có chắc chắn muốn xóa thông báo này?');">
                   <i class="fas fa-trash me-1"></i>Xóa
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>
