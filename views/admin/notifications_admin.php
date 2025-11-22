<?php
// Tệp này giả định nằm trong views/admin/
include 'header.php'; 
require_once '../../functions/NotificationFunctions.php';

$notiFn = new NotificationFunctions();
$notifications = $notiFn->getAllNotifications(); 
?>

<div class="container-fluid">
    <h1 class="mt-4">🔔 Quản lý Thông báo</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard_admin.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Thông báo</li>
    </ol>
    
    <div class="mb-3">
        <a href="create_notification.php" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Soạn Thông báo Mới
        </a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?php echo (strpos($_GET['msg'], 'error') !== false) ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
            <?php 
                echo [
                    'added' => 'Thông báo đã được gửi thành công!',
                    'deleted' => 'Thông báo đã được xóa thành công.',
                    'add_error' => 'Lỗi khi gửi thông báo.',
                    'delete_error' => 'Lỗi khi xóa thông báo.'
                ][$_GET['msg']] ?? 'Thao tác hoàn tất.';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Danh sách Thông báo
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Người Gửi</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($notifications):
                        foreach ($notifications as $notif): 
                            // SỬA: Dùng cột sender_id
                            $creatorName = $notiFn->getCreatorFullname($notif['sender_id']); 
                            $statusClass = $notif['status'] == 'read' ? 'success' : 'danger';
                            $statusText = $notif['status'] == 'read' ? 'Đã đọc' : 'Chưa đọc';
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($notif['noti_id']); ?></td> 
                        <td><?php echo htmlspecialchars($notif['title']); ?></td>
                        <td><?php echo htmlspecialchars($creatorName); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($notif['created_at'])); ?></td>
                        <td><span class="badge bg-<?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                        <td>
                            <a href="view_notification.php?id=<?php echo $notif['noti_id']; ?>" class="btn btn-sm btn-info">Xem</a> 
                            
                            <a href="../../handles/handle_notification.php?delete_id=<?php echo $notif['noti_id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa thông báo ID #<?php echo $notif['noti_id']; ?> này?');">Xóa</a> 
                        </td>
                    </tr>
                    <?php 
                        endforeach;
                    else:
                    ?>
                    <tr><td colspan="6" class="text-center">Chưa có thông báo nào được tạo.</td></tr>
                    <?php
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
include 'footer.php'; 
?>
