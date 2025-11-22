<?php
// lecturer/dashboard_lecturer.php

// 1. Khởi tạo session và kiểm tra quyền truy cập
session_start();

// Đảm bảo vai trò là 'giangvien' và có thông tin user
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'giangvien') {
    // Sửa đường dẫn tuyệt đối/tương đối để tránh lỗi 404
    header("Location: ../../login.php"); 
    exit;
}

// 2. Load các tệp cần thiết (Sử dụng include/require_once riêng cho lecturer)
// GIẢ ĐỊNH: Đã có thư mục 'includes/' ở cấp gốc để chứa header/footer/sidebar dùng chung
require_once '../../functions/db_connect.php'; 
require_once '../../functions/ScheduleFunctions.php'; 
require_once '../../functions/NotificationFunctions.php'; 

// Load giao diện (Tách khỏi admin)
// TẠO FILE ĐỘC LẬP: include '../includes/lecturer_header.php'; 
// TẠO FILE ĐỘC LẬP: include 'lecturer_sidebar.php'; 

// Sử dụng code HTML/Bootstrap trực tiếp để đơn giản hóa ví dụ
?>

<?php
$conn = connectDB();
$scheduleFn = new ScheduleFunctions();
$notiFn = new NotificationFunctions();

$lecturer_id = $_SESSION['user']['user_id'];
$lecturer_name = $_SESSION['user']['fullname'];

// Lấy dữ liệu cần thiết cho Giảng viên:
// 1. Số lượng lịch dạy hôm nay
$today = date('Y-m-d');
$total_schedules_today = $scheduleFn->countSchedulesByLecturerAndDate($lecturer_id, $today); 

// 2. Số lượng lớp học đang phụ trách (Giả định có hàm này)
$total_classes = 5; // Dùng giá trị giả định

// 3. Thông báo chung mới nhất (lấy 3 cái)
// Giả định hàm getAllNotifications() có thể hoạt động mà không cần user_id (thông báo chung)
$latest_notifications = $notiFn->getAllNotifications(3); 

// 4. Lấy phòng đang dạy ca gần nhất (Cần hàm mới)
$current_room = $scheduleFn->getCurrentTeachingRoom($lecturer_id); 
$current_room_display = $current_room['room_name'] ?? 'N/A';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS bổ sung */
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 71, 160, 0.2); /* Tăng hiệu ứng hover */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .sidebar-heading {
             padding: 1rem;
             font-size: 1.2rem;
             font-weight: bold;
             color: #0047a0;
             border-bottom: 1px solid #ccc;
        }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
    <div class="bg-light border-right" id="sidebar-wrapper" style="width: 250px;">
        <div class="sidebar-heading">Quản Lý Phòng Học</div>
        <div class="list-group list-group-flush">
            <a href="dashboard_lecturer.php" class="list-group-item list-group-item-action bg-light active"><i class="fas fa-home me-2"></i> Trang chủ</a> 
            <a href="profile.php" class="list-group-item list-group-item-action bg-light"><i class="fas fa-user-circle me-2"></i> Thông tin tài khoản</a>
            <a href="room_lookup.php" class="list-group-item list-group-item-action bg-light"><i class="fas fa-school me-2"></i> Tra cứu Phòng học</a>
            <a href="schedule.php" class="list-group-item list-group-item-action bg-light"><i class="fas fa-calendar-alt me-2"></i> Lịch dạy cá nhân</a>
            <a href="classes.php" class="list-group-item list-group-item-action bg-light"><i class="fas fa-calendar-alt me-2"></i> Thông tin lớp học</a>
            <a href="../../logout.php" class="list-group-item list-group-item-action bg-light text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a>
        </div>
    </div>
    
    <div id="page-content-wrapper" class="flex-grow-1">

        <div class="container-fluid px-4 py-4">
            <h2 class="fw-bold mb-4" style="color: #0047a0;">📚 Trang chủ </h2>
            <p class="text-muted">Chào mừng, **<?= htmlspecialchars($lecturer_name) ?>**! Đây là tổng quan công việc của bạn.</p>

            <div class="row g-4 mb-5">
                
                <div class="col-md-4">
                    <div class="card shadow-lg border-0 rounded-4 p-4 text-center" style="background: linear-gradient(135deg, #108dc7, #5c99e7); color: white;">
                        <h5 class="mb-2 fw-semibold"><i class="fas fa-clock me-1"></i> Lịch dạy Hôm nay</h5>
                        <h1 class="fw-bold"><?= $total_schedules_today ?></h1>
                        <p class="mb-0">Ca học trong ngày</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-lg border-0 rounded-4 p-4 text-center" style="background: linear-gradient(135deg, #a8e063, #56ab2f); color: white;">
                        <h5 class="mb-2 fw-semibold"><i class="fas fa-users me-1"></i> Tổng số Lớp học</h5>
                        <h1 class="fw-bold"><?= $total_classes ?></h1>
                        <p class="mb-0">Lớp học đang phụ trách</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-lg border-0 rounded-4 p-4 text-center" style="background: linear-gradient(135deg, #ff416c, #ff4b2b); color: white;">
                        <h5 class="mb-2 fw-semibold"><i class="fas fa-door-open me-1"></i> Phòng đang dạy</h5>
                        <h1 class="fw-bold"><?= htmlspecialchars($current_room_display) ?></h1>
                        <p class="mb-0">Ca gần nhất</p>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-primary text-white rounded-top-4">
                            <h5 class="mb-0"><i class="fas fa-calendar-check me-1"></i> Lịch Dạy Hôm Nay (<?= date('d/m/Y') ?>)</h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            // Lấy chi tiết lịch dạy hôm nay
                            $schedules_today = $scheduleFn->getSchedulesByLecturerAndDate($lecturer_id, $today);
                            
                            if (!empty($schedules_today)): 
                            ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($schedules_today as $schedule): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($schedule['subject_name'] ?? 'Môn học') ?></h6>
                                            <p class="mb-0 small text-muted">
                                                Phòng: **<?= htmlspecialchars($schedule['room_name'] ?? 'N/A') ?>** | Ca: <?= htmlspecialchars($schedule['start_time']) ?> - <?= htmlspecialchars($schedule['end_time']) ?>
                                            </p>
                                        </div>
                                        <span class="badge bg-info text-dark">Ca dạy</span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="mt-3 text-end">
                                    <a href="schedule.php" class="btn btn-sm btn-outline-primary">Xem toàn bộ lịch dạy</a>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success text-center mb-0" role="alert">
                                    <i class="fas fa-check-circle me-1"></i> Hôm nay bạn không có lịch dạy nào!
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-warning text-dark rounded-top-4">
                            <h5 class="mb-0"><i class="fas fa-bullhorn me-1"></i> Thông báo Mới</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($latest_notifications)): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($latest_notifications as $notif): ?>
                                    <li class="list-group-item">
                                        <a href="view_notification.php?id=<?= $notif['noti_id'] ?>" class="text-decoration-none d-block"> 
                                            <h6 class="mb-1 fw-bold text-dark"><?= htmlspecialchars($notif['title']) ?></h6>
                                            <p class="mb-0 small text-muted">
                                                <?= date('H:i d/m/Y', strtotime($notif['created_at'])) ?>
                                            </p>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="mt-3 text-end">
                                    <a href="notification.php" class="btn btn-sm btn-outline-secondary">Tất cả thông báo</a>
                                </div>
                            <?php else: ?>
                                <p class="text-center text-muted mb-0">Không có thông báo mới.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Script để bật/tắt sidebar (dùng cho mobile hoặc desktop nhỏ)
    document.getElementById("menu-toggle").addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById("wrapper").classList.toggle("toggled");
    });
</script>

<?php 
// Giả định footer nằm ở cấp thư mục cha (../footer.php) hoặc sử dụng footer độc lập
// include '../admin/footer.php'; 
?>
</body>
</html>
