<?php
session_start();
require_once(__DIR__ . '/../../functions/db_connect.php');

$conn = connectDB();

// =======================
// 1. Thống kê số lần phòng được sử dụng
// =======================
$sql_usage = "
    SELECT r.room_id, r.room_name, r.type, 
           COUNT(s.schedule_id) AS usage_count,
           SUM(TIMESTAMPDIFF(HOUR, s.start_time, s.end_time)) AS total_hours
    FROM rooms r
    LEFT JOIN schedules s ON r.room_id = s.room_id
    GROUP BY r.room_id, r.room_name, r.type
    ORDER BY usage_count DESC
";
$result_usage = $conn->query($sql_usage);

// =======================
// 2. Thống kê tổng hợp
// =======================
$total_hours_sql = "SELECT SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) AS total_hours FROM schedules";
$total_hours_res = $conn->query($total_hours_sql)->fetch_assoc();
$total_hours = $total_hours_res['total_hours'] ?? 0;

$total_requests_sql = "SELECT COUNT(*) AS total_requests FROM room_requests";
$total_requests = $conn->query($total_requests_sql)->fetch_assoc()['total_requests'] ?? 0;

$total_rejected_sql = "SELECT COUNT(*) AS total_rejected FROM room_requests WHERE status='rejected'";
$total_rejected = $conn->query($total_rejected_sql)->fetch_assoc()['total_rejected'] ?? 0;

$top_lecturer_sql = "
    SELECT u.fullname, COUNT(*) AS request_count
    FROM room_requests rr
    JOIN users u ON rr.lecturer_id = u.user_id
    GROUP BY u.user_id
    ORDER BY request_count DESC
    LIMIT 1
";
$top_lecturer_res = $conn->query($top_lecturer_sql)->fetch_assoc();
$top_lecturer = $top_lecturer_res['fullname'] ?? 'Không có dữ liệu';

// =======================
// 3. Xử lý form báo cáo & xuất file plaintext
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_report'])) {
    $name = trim($_POST['fullname']);
    $age = intval($_POST['age']);
    $role = $_SESSION['user']['role'] ?? 'Không rõ';
    $recommendation = trim($_POST['recommendation'] ?? '');

    $report_content = "Họ và tên: $name\n";
    $report_content .= "Chức vụ: $role\n";
    $report_content .= "Tuổi: $age\n\n";
    $report_content .= "Báo cáo quản lý phòng học\n";
    $report_content .= "1. Tổng số giờ sử dụng: $total_hours giờ\n";
    $report_content .= "2. Tổng số yêu cầu nhận được: $total_requests\n";
    $report_content .= "3. Tổng số yêu cầu bị từ chối: $total_rejected\n";
    $report_content .= "4. Giảng viên yêu cầu phòng nhiều nhất: $top_lecturer\n";
    $report_content .= "Kiến nghị: $recommendation\n";

    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="bao_cao_quan_ly_phong.txt"');
    echo $report_content;
    exit;
}
?>

<!DOCTYPE html>

<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Báo cáo & Thống kê phòng học</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
        justify-content: center;
        padding: 30px 0;
    }
    .report-container {
        width: 100%;
        max-width: 1000px;
    }
    h2, h4 {
        color: #fff;
        text-align: center;
    }
    .card {
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .card-header {
        font-weight: 600;
        font-size: 1.1rem;
    }
    table th, table td {
        vertical-align: middle;
    }
    table {
        margin-bottom: 0;
    }
    .form-control, .btn {
        border-radius: 50px;
    }
    .btn-primary {
        background: #1cc88a;
        border: none;
    }
    .btn-primary:hover {
        background: #17a673;
    }
    .btn-secondary {
        border-radius: 50px;
    }
</style>
</head>
<body>

<div class="report-container">

<div class="card shadow-sm p-4">
    <h2 class="mb-3 mb-3 text-dark">📊 Báo cáo & Thống kê phòng học</h2>

    <h4 class="mt-3 mb-3 text-dark">Số lần phòng được sử dụng</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Tên phòng</th>
                    <th>Loại phòng</th>
                    <th>Số lần đã sử dụng</th>
                    <th>Tổng số giờ sử dụng</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result_usage->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['room_name']) ?></td>
                        <td><?= htmlspecialchars($row['type']) ?></td>
                        <td><?= $row['usage_count'] ?></td>
                        <td><?= $row['total_hours'] ?? 0 ?> giờ</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <h4 class="mt-5 mb-3 text-dark">2. Soạn báo cáo</h4>
    <form method="post">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" name="fullname" class="form-control" placeholder="Họ và tên" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="age" class="form-control" placeholder="Tuổi" required>
            </div>
            <div class="col-md-12">
                <textarea name="recommendation" class="form-control" rows="3" placeholder="Kiến nghị"></textarea>
            </div>
        </div>
        <div class="mt-3 d-flex justify-content-between">
            <button type="submit" name="submit_report" class="btn btn-primary"><i class="fas fa-download me-1"></i> Lưu & Tải báo cáo</button>
            <a href="dashboard_admin.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại trang chủ</a>
        </div>
    </form>
</div>
```

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
