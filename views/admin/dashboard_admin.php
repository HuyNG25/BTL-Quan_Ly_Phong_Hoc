<?php 
session_start(); // Đảm bảo session được khởi động
require_once '../../functions/db_connect.php';
require_once '../../functions/ScheduleFunctions.php'; // CẦN INCLUDE FILE LOGIC XỬ LÝ

// =========================================================================
//                           LOGIC XỬ LÝ POST REQUEST (ĐẶT PHÒNG)
// =========================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Nếu hành động là YÊU CẦU ĐẶT PHÒNG từ giảng viên
    if ($_POST['action'] === 'request_booking') {
        
        $sFn = new ScheduleFunctions();
        
        // Lấy dữ liệu từ form
        // Đảm bảo các trường này tồn tại trong form room_lookup.php đã sửa
        $room_id = intval($_POST['room_id'] ?? 0);
        $user_id = intval($_POST['user_id'] ?? 0); 
        $subject_id = intval($_POST['subject_id'] ?? 0); 
        $date = trim($_POST['date'] ?? '');
        $start_time_only = trim($_POST['start_time'] ?? '');
        $end_time_only = trim($_POST['end_time'] ?? '');
        $note = trim($_POST['note'] ?? ''); 
        
        // Kiểm tra dữ liệu bắt buộc
        if ($room_id > 0 && $user_id > 0 && $subject_id > 0 && $date && $start_time_only && $end_time_only) {
            
            // Kết hợp ngày và giờ thành DATETIME format cho MySQL
            $start_time = $date . ' ' . $start_time_only;
            $end_time = $date . ' ' . $end_time_only;

            // Gọi hàm thêm lịch dạy
            $res = $sFn->addSchedule($room_id, $user_id, $subject_id, $start_time, $end_time, $note);
            
            if ($res === true) {
                $_SESSION['success_message'] = "✅ Yêu cầu đặt phòng đã được gửi thành công.";
            } elseif ($res === "conflict") {
                $_SESSION['error_message'] = "❌ Phòng học đã có lịch trong khoảng thời gian này. Vui lòng chọn giờ khác.";
            } else {
                $_SESSION['error_message'] = "⚠️ Lỗi khi gửi yêu cầu đặt phòng.";
            }
        } else {
             $_SESSION['error_message'] = "❌ Dữ liệu đặt phòng không hợp lệ hoặc thiếu thông tin.";
        }
        
        // Sau khi xử lý, chuyển hướng người dùng về trang tra cứu phòng học
        header("Location: ../views/room_lookup.php");
        exit;
    }

    // ... (Thêm các logic xử lý POST khác nếu có)
}

// =========================================================================
//                           HIỂN THỊ DASHBOARD (Dashboard)
// =========================================================================

// Nếu không phải là POST request xử lý hành động, thì tiếp tục hiển thị Dashboard
$conn = connectDB();

// Lấy dữ liệu thống kê
$result_rooms = $conn->query("SELECT COUNT(*) as total FROM rooms");
$result_users = $conn->query("SELECT COUNT(*) as total FROM users");
$result_schedules = $conn->query("SELECT COUNT(*) as total FROM schedules");

$total_rooms = $result_rooms->fetch_assoc()['total'];
$total_users = $result_users->fetch_assoc()['total'];
$total_schedules = $result_schedules->fetch_assoc()['total'];
closeDB($conn); // Đóng kết nối sau khi lấy dữ liệu

?>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="container-fluid px-4 py-4">
    <h2 class="fw-bold mb-4 text-primary">📊 Tổng quan hệ thống</h2>

        <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-lg border-0 rounded-4 p-4 text-center" style="background: linear-gradient(135deg, #00b4db, #0083b0); color: white;">
                <h5 class="mb-2">Phòng học</h5>
                <h1 class="fw-bold"><?= $total_rooms ?></h1>
                <i class="bi bi-building fs-2"></i>
            </div>
            
        </div>
        <div class="col-md-4">
            <div class="card shadow-lg border-0 rounded-4 p-4 text-center" style="background: linear-gradient(135deg, #56ab2f, #a8e063); color: white;">
                <h5 class="mb-2">Người dùng</h5>
                <h1 class="fw-bold"><?= $total_users ?></h1>
                <i class="bi bi-people fs-2"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-lg border-0 rounded-4 p-4 text-center" style="background: linear-gradient(135deg, #ff416c, #ff4b2b); color: white;">
                <h5 class="mb-2">Lịch học</h5>
                <h1 class="fw-bold"><?= $total_schedules ?></h1>
                <i class="bi bi-calendar-check fs-2"></i>
            </div>
        </div>
    </div>

        <div class="card mt-5 shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
            <h5 class="mb-0">📅 Tóm tắt dữ liệu</h5>
        </div>
        <div class="card-body">
            <table class="table align-middle table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Hạng mục</th>
                        <th>Số lượng</th>
                        <th>Trạng thái hệ thống</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><i class="bi bi-building"></i> Phòng học</td>
                        <td><span class="fw-bold text-primary"><?= $total_rooms ?></span></td>
                        <td><span class="badge bg-success">Hoạt động tốt</span></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><i class="bi bi-people"></i> Người dùng</td>
                        <td><span class="fw-bold text-success"><?= $total_users ?></span></td>
                        <td><span class="badge bg-info text-dark">Ổn định</span></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><i class="bi bi-calendar3"></i> Lịch học</td>
                        <td><span class="fw-bold text-danger"><?= $total_schedules ?></span></td>
                        <td><span class="badge bg-warning text-dark">Đang cập nhật</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
body {
    background: #f5f7fa;
}
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}
</style>

<?php include 'footer.php'; ?>

