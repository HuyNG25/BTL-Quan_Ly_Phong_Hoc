<?php
session_start();
require_once('../../functions/db_connect.php');
require_once('../../functions/ScheduleFunctions.php'); 

// Kiểm tra session và role giảng viên
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'giangvien') {
    header('Location: ../../login.php');
    exit();
}

$lecturer_id = $_SESSION['user']['user_id'];
$sFn = new ScheduleFunctions();

// Lấy tất cả lịch của giảng viên
$allSchedules = $sFn->getAllSchedules();
$personalSchedules = array_filter($allSchedules, function($s) use ($lecturer_id) {
    return $s['user_id'] == $lecturer_id;
});

// Lấy danh sách môn giảng viên phụ trách
$subjects = $sFn->getSubjectsByLecturerId($lecturer_id);
$subjectsMap = [];
foreach ($subjects as $sub) {
    $subjectsMap[$sub['subject_id']] = $sub['subject_name'];
}
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
</style>
</head>
<body>
<div class="container my-5">

<h1>📅 Lịch Dạy Cá Nhân (<?= htmlspecialchars($_SESSION['user']['fullname']) ?>)</h1>

<div class="card">
    <div class="card-header">Lịch Dạy Cá Nhân</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Ngày</th>
                        <th>Giờ Bắt đầu</th>
                        <th>Giờ Kết thúc</th>
                        <th>Phòng học</th>
                        <th>Môn học</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($personalSchedules)): ?>
                    <?php foreach ($personalSchedules as $s): ?>
                        <tr class="text-center">
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($s['date'] ?? $s['start_time']))) ?></td>
                            <td><?= htmlspecialchars(date('H:i', strtotime($s['start_time']))) ?></td>
                            <td><?= htmlspecialchars(date('H:i', strtotime($s['end_time']))) ?></td>
                            <td><?= htmlspecialchars($s['room_name'] ?? 'Chưa có phòng') ?></td>
                            <td>
                                <?= htmlspecialchars($subjectsMap[$s['user_id']] ?? $s['subject_name']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted">Không có lịch dạy được phân công.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="../../views/lecturer/dashboard_lecturer.php" class="btn btn-sm btn-light">🏠 Trang Chủ</a>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
