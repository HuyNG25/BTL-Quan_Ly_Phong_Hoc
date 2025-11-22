<?php
// Tệp này giả định nằm trong views/admin/
include 'header.php'; 
require_once '../../functions/NotificationFunctions.php';

$notiFn = new NotificationFunctions();
$notification = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 
    $notification = $notiFn->getNotificationById($id); // Lấy theo noti_id
}

if (!$notification):
?>
    <div class="container-fluid">
        <h1 class="mt-4">Thông báo không tồn tại</h1>
        <p>Thông báo bạn đang tìm kiếm không có sẵn hoặc đã bị xóa.</p>
        <a href="notifications_admin.php" class="btn btn-primary">Quay lại danh sách Thông báo</a>
    </div>
<?php else: 
    // SỬA: Dùng cột sender_id
    $creatorName = $notiFn->getCreatorFullname($notification['sender_id']); 
?>

<div class="container-fluid">
    <h1 class="mt-4">📰 Chi tiết Thông báo</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="notifications_admin.php">Thông báo</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h2 class="card-title mb-0"><?php echo htmlspecialchars($notification['title']); ?></h2>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                <strong>Người Gửi:</strong> <?php echo htmlspecialchars($creatorName); ?> | 
                <strong>Thời gian:</strong> <?php echo date('H:i:s d/m/Y', strtotime($notification['created_at'])); ?> |
                <strong>Trạng thái:</strong> 
                <span class="badge bg-<?php echo $notification['status'] == 'read' ? 'success' : 'danger'; ?>">
                    <?php echo $notification['status'] == 'read' ? 'Đã đọc' : 'Chưa đọc'; ?>
                </span>
            </p>
            <hr>

            <div class="notification-content">
                <?php echo nl2br(htmlspecialchars($notification['message'])); ?>
            </div>

        </div>
        <div class="card-footer">
            <a href="notifications_admin.php" class="btn btn-secondary">Quay lại</a>
            <a href="../../handlers/handle_notification.php?delete_id=<?php echo $notification['noti_id']; ?>" class="btn btn-danger float-end" 
               onclick="return confirm('Bạn có chắc chắn muốn xóa thông báo này?');">Xóa Thông báo</a>
        </div>
    </div>
</div>

<?php endif; 
include 'footer.php'; 
?>