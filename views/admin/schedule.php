<?php
include 'header.php';
include 'sidebar.php';

require_once '../../functions/ScheduleFunctions.php';
require_once '../../functions/LecturerFunctions.php';
require_once '../../functions/db_connect.php';

$conn = connectDB();
$scheduleFn = new ScheduleFunctions();
$schedules = $scheduleFn->getAllSchedules();

// Lấy tất cả môn học để tạo map [subject_id => subject_name]
$allSubjects = getAllSubjects($conn);
$subjectMap = array_column($allSubjects, 'subject_name', 'subject_id');

closeDB($conn);
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4 text-primary">📅 Danh sách Lịch dạy của giảng viên</h3>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>Phòng</th>
                                    <th>Giảng viên</th>
                                    <th>Môn học</th>
                                    <th>Bắt đầu</th>
                                    <th>Kết thúc</th>
                                    <th>Ghi chú</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($schedules)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">Chưa có lịch học nào được tạo.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($schedules as $s): ?>
                                        <tr>
                                            <td class="text-center"><?= $s['schedule_id'] ?></td>
                                            <td><?= htmlspecialchars($s['room_name'] ?? $s['room_id']) ?></td>
                                            <td><?= htmlspecialchars($s['lecturer_name'] ?? $s['user_id']) ?></td>
                                            <td><?= htmlspecialchars($subjectMap[$s['user_id']] ?? $s['subject_name']) ?></td>
                                            <td><?= date('H:i', strtotime($s['start_time'])) ?></td>
                                            <td><?= date('H:i', strtotime($s['end_time'])) ?></td>
                                            <td><?= htmlspecialchars($s['note']) ?></td>
                                            <td class="text-center text-nowrap">
                                                <a href="edit_schedule.php?id=<?= $s['schedule_id'] ?>" class="btn btn-sm btn-info me-2" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete_schedule.php?id=<?= $s['schedule_id'] ?>" class="btn btn-sm btn-danger" title="Xóa" 
                                                   onclick="return confirm('Bạn có chắc chắn muốn xóa lịch học ID <?= $s['schedule_id'] ?>?');">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
