<?php
session_start();
require_once('../../functions/db_connect.php');
require_once('../../functions/LecturerFunctions.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'giangvien') {
    header('Location: ../../login.php');
    exit();
}

$conn = connectDB();
$lecturer_id = $_SESSION['user']['user_id'];
$classes = getAllClasses($conn); // Lấy danh sách lớp học nếu cần

closeDB($conn);
?>

<!DOCTYPE html>

<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>📅 Lịch Dạy Cá Nhân</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
    body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    h1 { color: #34495e; margin-bottom: 30px; text-align: center; }
    .card { border-radius: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .card-header { background: linear-gradient(90deg, #4e73df, #1cc88a); color: #fff; font-weight: 600; font-size: 1.2rem; }
    table.table-hover tbody tr:hover { background-color: #d6f5f5; }
    .message { padding: 10px 15px; border-radius: 0.5rem; margin-bottom: 20px; }
    .message.success { background-color: #d4edda; color: #155724; }
    .message.error { background-color: #f8d7da; color: #721c24; }
</style>
</head>
<body>
<div class="container my-5">

<h1>📅 Lịch Dạy Cá Nhân (<?= htmlspecialchars($_SESSION['user']['fullname']) ?>)</h1>

<?php if (isset($_SESSION['success_message'])): ?>

```
<div class="message success"><?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
```

<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>

```
<div class="message error"><?= htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></div>
```

<?php endif; ?>

<div class="card">
    <div class="card-header">Lịch Dạy Cá Nhân</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Ngày & Giờ Bắt đầu</th>
                        <th>Giờ kết thúc</th>
                        <th>Phòng học</th>
                        <th>Môn học</th>
                        <th>Lớp</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($schedules)): ?>
                        <?php foreach ($schedules as $s): ?>
                            <tr>
                                <td><?= $s['start_time'] ?? '-'; ?></td>
                                <td><?= $s['end_time'] ?? '-'; ?></td>
                                <td><?= $s['room_name'] ?? '-'; ?></td>
                                <td><?= $s['subject_name'] ?? '-'; ?></td>
                                <td><?= $s['class_name'] ?? '-'; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" disabled title="Chức năng đang phát triển">Yêu cầu Thay đổi</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Không có lịch dạy được phân công.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
</body>
</html>
