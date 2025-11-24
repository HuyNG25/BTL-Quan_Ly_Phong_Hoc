<?php
session_start();
require_once('../../functions/ScheduleFunctions.php');
// 🚨 BỔ SUNG: require RoomFunctions để lấy danh sách phòng
require_once('../../functions/RoomFunctions.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'giangvien') {
    header("Location: ../../login.php");
    exit;
}

$sFn = new ScheduleFunctions();
$rFn = new RoomFunctions(); // 🚨 KHỞI TẠO: Khởi tạo RoomFunctions

$id = intval($_GET['id']);
$s = $sFn->getScheduleById($id);

if (!$s) {
    die("Lịch không tồn tại");
}

// Lấy danh sách lớp
$classes = $sFn->getAllClasses();

// 🚨 LẤY DỮ LIỆU PHÒNG HỌC: Lấy danh sách tất cả các phòng
$rooms = $rFn->getAllRooms(); 

// 🚨 LẤY TÊN PHÒNG HIỆN TẠI: Dùng để hiển thị trong ô chọn
$current_room = null;
foreach ($rooms as $room) {
    if ($room['room_id'] == $s['room_id']) {
        $current_room = $room;
        break;
    }
}
$current_room_name = $current_room ? htmlspecialchars($current_room['room_name']) : 'Phòng không xác định';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa lịch dạy</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="p-4 bg-light">
<div class="container">
<h2 class="mb-4">✏️ Sửa Lịch Dạy</h2>

<form action="../../handles/handle_schedule.php" method="POST" class="card p-4 shadow-sm">
    <input type="hidden" name="update_schedule" value="1">
    <input type="hidden" name="schedule_id" value="<?= $s['schedule_id'] ?>">

    <div class="mb-3">
        <label class="form-label">Phòng học</label>
        
        <input type="hidden" id="room_id_input" name="room_id" value="<?= $s['room_id'] ?>"> 
        
        <div class="input-group">
            <input type="text" id="room_name_display" class="form-control" 
                   value="<?= $current_room_name ?>" readonly required>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#roomModal">
                Chọn phòng
            </button>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Giảng viên</label>
        <input type="number" name="user_id" class="form-control" value="<?= $s['user_id'] ?>" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">Môn học (subject_id)</label>
        <input type="number" name="subject_id" class="form-control" value="<?= $s['subject_id'] ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Lớp học</label>
        <select name="class_id" class="form-control">
            <option value="">-- Chọn lớp (không bắt buộc) --</option>
            <?php foreach ($classes as $c): ?>
                <option value="<?= $c['class_id'] ?>" <?= ($s['class_id'] == $c['class_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['class_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Giờ bắt đầu</label>
        <input type="datetime-local" name="start_time" class="form-control"
               value="<?= date('Y-m-d\TH:i', strtotime($s['start_time'])) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Giờ kết thúc</label>
        <input type="datetime-local" name="end_time" class="form-control"
               value="<?= date('Y-m-d\TH:i', strtotime($s['end_time'])) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Ghi chú</label>
        <textarea name="note" class="form-control" rows="3"><?= htmlspecialchars($s['note']) ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
    <a href="schedule.php" class="btn btn-secondary">Hủy</a>
</form>
</div>

<div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roomModalLabel">Chọn Phòng Học</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover table-bordered table-sm">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>ID</th>
                                <th>Tên Phòng</th>
                                <th>Sức chứa</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr data-room-id="<?= $room['room_id'] ?>" 
                                    data-room-name="<?= htmlspecialchars($room['room_name']) ?>">
                                    <td><?= $room['room_id'] ?></td>
                                    <td><?= htmlspecialchars($room['room_name']) ?></td>
                                    <td><?= $room['capacity'] ?? 'N/A' ?></td>
                                    <td><button type="button" class="btn btn-sm btn-success select-room-btn">Chọn</button></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($rooms)): ?>
                    <p class="text-center text-muted">Không tìm thấy phòng học nào trong hệ thống.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Sự kiện khi nhấn nút "Chọn" trong bảng Modal
    $('.select-room-btn').on('click', function() {
        // Lấy thông tin phòng từ hàng (tr) chứa nút bấm
        var row = $(this).closest('tr');
        var roomId = row.data('room-id');
        var roomName = row.data('room-name');

        // 1. Cập nhật giá trị ID vào input hidden (sẽ gửi lên server)
        $('#room_id_input').val(roomId);
        // 2. Cập nhật Tên phòng vào input hiển thị
        $('#room_name_display').val(roomName);

        // 3. Đóng Modal
        var roomModal = bootstrap.Modal.getInstance(document.getElementById('roomModal'));
        roomModal.hide();
    });
});
</script>

</body>
</html>
