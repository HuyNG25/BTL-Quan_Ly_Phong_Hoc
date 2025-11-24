<?php 
session_start();
require_once('../../functions/db_connect.php');
require_once('../../functions/ScheduleFunctions.php'); 

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'giangvien') {
    header('Location: ../../login.php');
    exit();
}

$lecturer_id = $_SESSION['user']['user_id'];
$sFn = new ScheduleFunctions();

$allSchedules = $sFn->getAllSchedules();
$personalSchedules = array_filter($allSchedules, function($s) use ($lecturer_id) {
    return $s['user_id'] == $lecturer_id;
});

// Môn học giảng viên dạy
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
<title>Lịch Dạy Cá Nhân</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
body { background:#f8f9fa; }
h1 { text-align:center; margin:25px 0; }
</style>
</head>
<body>
<div class="container">

<h1>📅 Lịch Dạy Cá Nhân (<?= $_SESSION['user']['fullname'] ?>)</h1>

<div class="card mb-3">
    <div class="card-header bg-primary text-white">Lịch Dạy</div>
    <div class="card-body p-0">
        <table class="table table-bordered table-striped mb-0">
            <thead class="table-dark text-center">
                <tr>
                    <th>Ngày</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Phòng</th>
                    <th>Môn</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($personalSchedules)): ?>
                <?php foreach ($personalSchedules as $s): ?>
                    <tr class="text-center">
                        <td><?= date('d/m/Y', strtotime($s['date'])) ?></td>
                        <td><?= date('H:i', strtotime($s['start_time'])) ?></td>
                        <td><?= date('H:i', strtotime($s['end_time'])) ?></td>
                        <td><?= $s['room_name'] ?></td>
                        <td><?= $s['subject_name'] ?></td>
                        <td>
                            <a href="schedule_edit.php?id=<?= $s['schedule_id'] ?>" class="btn btn-sm btn-warning">✏️ Sửa</a>
                            <a href="../../handles/handle_schedule.php?action=delete&id=<?= $s['schedule_id'] ?>" 
                               onclick="return confirm('Xác nhận xoá lịch này?');"
                               class="btn btn-sm btn-danger">🗑 Xoá</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted">Không có lịch.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<a href="../lecturer/dashboard_lecturer.php" class="btn btn-light">🏠 Trang Chủ</a>

</div>
</body>
</html>
