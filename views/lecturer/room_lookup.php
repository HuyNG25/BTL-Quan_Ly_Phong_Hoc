<?php
session_start();
require_once('../../functions/db_connect.php');
require_once('../../functions/LecturerFunctions.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'giangvien') {
    header('Location: ../../login.php');
    exit();
}

$conn = connectDB();
$rooms = getAllRooms($conn); 
$classes = getAllClasses($conn);
closeDB($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tra Cứu Phòng Học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background: #f4f6f9; }
        .page-title { font-size: 28px; font-weight: 700; margin-bottom: 15px; }
        #back-btn {
            position: fixed;
            bottom: 20px;
            left: 20px;
        }
    </style>
</head>

<body class="p-4">

    <div class="container">

        <h1 class="page-title">🏫 Tra Cứu Phòng Học & Đặt Phòng</h1>

        <!-- THÔNG BÁO -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
        <?php unset($_SESSION['success_message']); endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error_message']; ?></div>
        <?php unset($_SESSION['error_message']); endif; ?>


        <!-- TÌM KIẾM -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control"
                   placeholder="🔍 Tìm phòng theo tên, sức chứa, thiết bị...">
        </div>


        <!-- DANH SÁCH PHÒNG -->
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-primary text-white fw-bold">
                Danh Sách Phòng
            </div>

            <div class="card-body p-0">
                <table class="table table-hover mb-0" id="roomTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên Phòng</th>
                            <th>Sức chứa</th>
                            <th>Thiết bị</th>
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
                            <td><?= $room['equipment']; ?></td>

                            <td>
                                <span class="badge bg-info">Kiểm tra khi đặt</span>
                            </td>

                            <td>
                                <button 
                                    class="btn btn-sm btn-success"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#bookingModal"
                                    data-room="<?= $room['room_id']; ?>"
                                    data-roomname="<?= $room['room_name']; ?>"
                                >
                                    Đặt phòng
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
        <!-- MODAL ĐẶT PHÒNG -->

<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="../../handles/handle_admin_request.php" method="POST">
            <input type="hidden" name="action" value="request_booking">
            <input type="hidden" name="room_id" id="modal_room_id">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="room_title">Đặt phòng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- Mục chọn lớp học -->
                <label class="fw-bold">Chọn Lớp học:</label>
                <select name="class_id" class="form-select mb-3" required>
                    <option value="">-- Chọn lớp --</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['class_id']; ?>"><?= $class['class_name']; ?></option>
                    <?php endforeach; ?>
                </select>

                <label class="fw-bold">Ngày đặt:</label>
                <input type="date" name="date" class="form-control" required>

                <label class="fw-bold mt-3">Giờ bắt đầu:</label>
                <input type="time" name="start_time" class="form-control" required>

                <label class="fw-bold mt-3">Giờ kết thúc:</label>
                <input type="time" name="end_time" class="form-control" required>

                <label class="fw-bold mt-3">Mục đích sử dụng:</label>
                <textarea name="purpose" class="form-control" rows="3" required></textarea>

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success px-4">Gửi yêu cầu</button>
            </div>

        </form>

    </div>
</div>
```

</div>


    </div>


    <!-- NÚT QUAY LẠI -->
    <a id="back-btn" href="dashboard_lecturer.php" class="btn btn-secondary">
        ⬅ Quay lại
    </a>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Đổ dữ liệu phòng vào modal
        const bookingModal = document.getElementById('bookingModal');
        bookingModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const roomID = button.getAttribute('data-room');
            const roomName = button.getAttribute('data-roomname');

            document.getElementById('modal_room_id').value = roomID;
            document.getElementById('room_title').innerText = "Đặt phòng: " + roomName;
        });


        // TÌM KIẾM REALTIME
        document.getElementById("searchInput").addEventListener("keyup", function () {
            const keyword = this.value.toLowerCase();
            const rows = document.querySelectorAll("#roomTable tbody tr");

            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(keyword) ? "" : "none";
            });
        });
    </script>

</body>
</html>
