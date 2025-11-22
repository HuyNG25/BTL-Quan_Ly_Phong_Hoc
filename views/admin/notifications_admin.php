<?php
// views/admin/notifications_admin.php
include 'header.php';
require_once('../../functions/NotificationFunctions.php');

$notiFn = new NotificationFunctions();
$notifications = $notiFn->getAllNotifications();
?>

<div class="container-fluid mt-4">
    <h1 class="text-center mb-4" style="color:#fff;">🔔 Quản lý Thông báo</h1>
    
    <div class="mb-3">
        <a href="create_notification.php" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Soạn Thông báo Mới
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white" style="font-weight:600;">
            <i class="fas fa-table me-1"></i> Danh sách Thông báo
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Người gửi</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($notifications): ?>
                        <?php foreach($notifications as $notif): 
                            $creatorName = $notiFn->getCreatorFullname($notif['sender_id']);
                            $statusClass = $notif['status'] == 'read' ? 'success' : 'danger';
                            $statusText = $notif['status'] == 'unread' ? 'Đã đọc' : 'Chưa đọc';
                            $rowClass = $notif['status'] == 'read' ? '' : 'table-warning'; // highlight chưa đọc
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td><?= htmlspecialchars($notif['noti_id']) ?></td>
                            <td><?= htmlspecialchars($notif['title']) ?></td>
                            <td><?= htmlspecialchars($creatorName) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></td>
                            <td><span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span></td>
                            <td>
                                <a href="view_notification.php?id=<?= $notif['noti_id'] ?>" class="btn btn-sm btn-info">
                                    Xem
                                </a>
                                <a href="../../handles/handle_notification.php?delete_id=<?= $notif['noti_id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa thông báo này?');">
                                   Xóa
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Chưa có thông báo nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
body {
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.table-warning {
    font-weight: 600;
}
.btn {
    border-radius: 50px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>
