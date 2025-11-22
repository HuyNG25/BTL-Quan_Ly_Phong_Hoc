<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php
require_once '../../functions/db_connect.php';
$conn = connectDB();

// Lấy dữ liệu thống kê
$result_rooms = $conn->query("SELECT COUNT(*) as total FROM rooms");
$result_users = $conn->query("SELECT COUNT(*) as total FROM users");
$result_schedules = $conn->query("SELECT COUNT(*) as total FROM schedules");

$total_rooms = $result_rooms->fetch_assoc()['total'];
$total_users = $result_users->fetch_assoc()['total'];
$total_schedules = $result_schedules->fetch_assoc()['total'];
?>

<!-- ====== Giao diện Dashboard ====== -->
<div class="container-fluid px-4 py-4">
    <h2 class="fw-bold mb-4 text-primary">📊 Tổng quan hệ thống</h2>

    <!-- Thẻ thống kê -->
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

    <!-- Phần bảng tổng hợp nhỏ -->
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

<!-- CSS bổ sung -->
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
