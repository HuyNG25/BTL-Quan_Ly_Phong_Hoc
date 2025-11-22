<?php
session_start();
require_once('../../functions/db_connect.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'giangvien') {
    header('Location: ../../login.php');
    exit();
}

$conn = connectDB();
$lecturer_id = $_SESSION['user']['user_id'];

// Xử lý hành động xem hoặc xoá
if (isset($_GET['action'], $_GET['id'])) {
    $notif_id = intval($_GET['id']);
    if ($_GET['action'] === 'view') {
        $stmt = $conn->prepare("UPDATE notifications SET status='read' WHERE noti_id=? AND receiver_id=?");
        $stmt->bind_param("ii", $notif_id, $lecturer_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($_GET['action'] === 'delete') {
        $stmt = $conn->prepare("DELETE FROM notifications WHERE noti_id=? AND receiver_id=?");
        $stmt->bind_param("ii", $notif_id, $lecturer_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: notification.php");
    exit();
}

// Lấy danh sách thông báo của giảng viên
$stmt = $conn->prepare("SELECT * FROM notifications WHERE receiver_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
closeDB($conn);
?>

<!DOCTYPE html>

<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông báo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Tất cả thông báo</h2>
    <?php if (empty($notifications)): ?>
        <p>Chưa có thông báo nào.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tiêu đề</th>
                    <th>Nội dung</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($notifications as $notif): ?>
                <tr>
                    <td><?= htmlspecialchars($notif['title']) ?></td>
                    <td><?= htmlspecialchars($notif['message']) ?></td>
                    <td>
                        <?php if ($notif['status'] === 'unread'): ?>
                            <span class="badge bg-danger">Chưa đọc</span>
                        <?php else: ?>
                            <span class="badge bg-success">Đã đọc</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $notif['created_at'] ?></td>
                    <td>
                        <a href="notification.php?action=view&id=<?= $notif['noti_id'] ?>" class="btn btn-primary btn-sm">Xem</a>
                        <a href="notification.php?action=delete&id=<?= $notif['noti_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xoá thông báo này?');">Xoá</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<div class="mb-3">
        <a href="http://localhost/QuanLyPhongHoc/views/lecturer/dashboard_lecturer.php" class="btn btn-secondary">&larr; Quay lại trang chủ</a>
    </div>
</body>
</html>
