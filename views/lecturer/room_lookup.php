<?php
session_start();
require_once('../../functions/db_connect.php');
require_once('../../functions/LecturerFunctions.php');
require_once('../../functions/ScheduleFunctions.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'giangvien') {
    header('Location: ../../login.php');
    exit();
}

$conn = connectDB();
$lecturer_id = $_SESSION['user']['user_id'];

$rooms = getAllRooms($conn); 
$scheduleFn = new ScheduleFunctions();
$subjects = $scheduleFn->getSubjectsByLecturerId($lecturer_id); 
$classes = $scheduleFn->getAllClasses(); 

closeDB($conn);
?>

<!DOCTYPE html>

<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tra Cứu Phòng Học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: #f4f6f9; 
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .page-title { font-size: 28px; font-weight: 700; margin-bottom: 20px; }
        .card { border-radius: 15px; }
        .table-hover tbody tr:hover { background: rgba(0,123,255,0.05); }

        /* Scroll + Sticky header */
        .table-responsive {
            max-height: 450px;
            overflow-y: auto;
        }
        thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #f8f9fa !important;
        }
    </style>
</head>
<body class="p-4">

<div class="container">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">🏫 Tra Cứu Phòng Học & Đặt Phòng</h1>
    <a href="dashboard_lecturer.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Quay về trang chủ
    </a>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
<?php unset($_SESSION['success_message']); endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error_message']; ?></div>
<?php unset($_SESSION['error_message']); endif; ?>

<div class="mb-3">
    <input type="text" id="searchInput" class="form-control rounded-pill"
           placeholder="🔍 Tìm phòng theo tên, sức chứa,...">
</div>

<div class="card shadow-sm mt-3">
    <div class="card-header bg-primary text-white fw-bold rounded-top-3">
        Danh Sách Phòng
    </div>

    <div class="card-body p-0">

        <!-- BẢNG CÓ SCROLL VÀ HEADER CỐ ĐỊNH -->
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="roomTable" style="border-collapse: separate; border-spacing: 0;">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên Phòng</th>
                        <th>Sức chứa</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($rooms as $room): ?>
                    <tr>
                        <td><?= $room['room_id']; ?></td>
                        <td class="fw-bold"><?= $room['room_name']; ?></td>
                        <td><?= $room['capacity']; ?></td>
                        <td><span class="badge bg-info">Kiểm tra khi đặt</span></td>
                        <td>
                            <button 
                                class="btn btn-sm btn-success"
                                data-bs-toggle="modal" 
                                data-bs-target="#bookingModal"
                                data-room="<?= $room['room_id']; ?>"
                                data-roomname="<?= $room['room_name']; ?>"
                            >Đặt phòng</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modal đặt phòng -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../handles/handle_admin_request.php" method="POST">
                <input type="hidden" name="action" value="request_booking">
                <input type="hidden" name="room_id" id="modal_room_id">
                <input type="hidden" name="user_id" value="<?= $_SESSION['user']['user_id']; ?>"> 
                
                <div class="modal-header bg-success text-white rounded-top-3">
                    <h5 class="modal-title" id="room_title">Đặt phòng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Chọn Môn học:</label>
                    <select name="subject_id" class="form-select mb-3" required>
                        <option value="">-- Chọn môn học --</option>
                        <?php if (!empty($subjects)) foreach ($subjects as $subject): ?>
                            <option value="<?= $subject['subject_id']; ?>"><?= $subject['subject_name']; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label">Chọn Lớp học:</label>
                    <select name="class_id" class="form-select mb-3" required>
                        <option value="">-- Chọn lớp học --</option>
                        <?php if (!empty($classes)) foreach ($classes as $class): ?>
                            <option value="<?= $class['class_id']; ?>"><?= $class['class_name']; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label">Ngày đặt:</label>
                    <input type="date" name="date" class="form-control mb-3" required>

                    <label class="form-label">Giờ bắt đầu:</label>
                    <input type="time" name="start_time" class="form-control mb-3" required>

                    <label class="form-label">Giờ kết thúc:</label>
                    <input type="time" name="end_time" class="form-control mb-3" required>

                    <label class="form-label">Mục đích sử dụng:</label>
                    <textarea name="note" class="form-control mb-0" rows="3" required></textarea>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success px-4">Gửi yêu cầu</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var bookingModal = document.getElementById('bookingModal');
    bookingModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var roomId = button.getAttribute('data-room');
        var roomName = button.getAttribute('data-roomname');
        bookingModal.querySelector('#room_title').textContent = 'Đặt Phòng: ' + roomName;
        bookingModal.querySelector('#modal_room_id').value = roomId;
    });

    document.getElementById('searchInput').addEventListener('keyup', function() {
        var filter = this.value.toUpperCase();
        var table = document.getElementById('roomTable');
        var tr = table.getElementsByTagName('tr');
        for (var i = 1; i < tr.length; i++) {
            tr[i].style.display =
                tr[i].textContent.toUpperCase().indexOf(filter) > -1
                ? "" : "none";
        }
    });
});
</script>

</body>
</html>

