<?php
session_start();
require_once(__DIR__ . '/../../functions/db_connect.php');
 // kết nối DB

$conn = connectDB(); // hàm kết nối DB của bạn

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
    $kiến_nghi = trim($_POST['recommendation'] ?? '');

    // Tạo nội dung báo cáo
    $report_content = "Họ và tên: $name\n";
    $report_content .= "Chức vụ: $role\n";
    $report_content .= "Tuổi: $age\n\n";
    $report_content .= "Bản báo cáo quản lý phòng học tại trường đại học Đại Nam\n";
    $report_content .= "1. Tổng số giờ sử dụng: $total_hours giờ\n";
    $report_content .= "2. Tổng số yêu cầu nhận được: $total_requests\n";
    $report_content .= "3. Tổng số yêu cầu bị từ chối: $total_rejected\n";
    $report_content .= "4. Giảng viên yêu cầu đăng ký phòng học nhiều lần nhất: $top_lecturer\n";
    $report_content .= "Kiến nghị: $kiến_nghi\n";

    // Tải file về
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
<title>Báo cáo & Thống kê phòng học</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container my-4">
    <h2>Báo cáo & Thống kê phòng học</h2>

```
<!-- Bảng thống kê sử dụng phòng -->
<h4 class="mt-4">1. Số lần phòng được sử dụng</h4>
<table class="table table-bordered">
    <thead>
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

<!-- Form soạn báo cáo -->
<h4 class="mt-5">2. Soạn báo cáo</h4>
<form method="post">
    <div class="mb-3">
        <label>Họ và tên</label>
        <input type="text" name="fullname" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Tuổi</label>
        <input type="number" name="age" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Kiến nghị</label>
        <textarea name="recommendation" class="form-control" rows="3"></textarea>
    </div>
    <button type="submit" name="submit_report" class="btn btn-primary">Lưu & Tải báo cáo</button>
</form>
```

</div>
</body>
</html>
