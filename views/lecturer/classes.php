<?php
// QUANLYPHONGHOC/views/lecturer/classes.php
session_start();

// Kiểm tra đăng nhập và vai trò
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'giangvien' && $_SESSION['user']['role'] !== 'admin')) {
    // Chuyển hướng về login.php ở thư mục gốc
    header('Location: ../../../login.php'); 
    exit();
}

// 1. Require db_connect.php (chứa hàm connectDB)
require_once '../../functions/db_connect.php';
// 2. Gọi hàm connectDB() để tạo ra biến $conn có giá trị
$conn = connectDB(); 

// 3. Require ClassFunctions.php (chứa định nghĩa Class)
require_once '../../functions/ClassFunctions.php'; 

// 4. Khởi tạo đối tượng $ClassFunctions với $conn đã được tạo
$ClassFunctions = new ClassFunctions($conn); 

// Lấy danh sách tất cả các lớp học
$classes = $ClassFunctions->getAllClasses(); 

// Đóng kết nối sau khi lấy dữ liệu xong
closeDB($conn);
?>

<?php 
// Giả định header.php và footer.php nằm ở views/admin/
include '../admin/header.php'; 
?>

<div class="container">
    <h2><i class="fas fa-chalkboard-teacher"></i> Thông tin Lớp học</h2>
    <p>Hiển thị chi tiết lớp học và lịch lên lớp đồng bộ.</p>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Mã Lớp</th>
                <th>Tên Lớp</th>
                <th>Môn Học</th>
                <th>Số Lượng SV</th>
                <th>Thời Gian Lên Lớp (Bắt đầu)</th>
                <th>Thời Gian Lên Lớp (Kết thúc)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($classes)): ?>
                <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($class['class_id']); ?></td>
                        <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                        <td><?php echo htmlspecialchars($class['subject_name'] ?? 'Chưa xác định'); ?></td>
                        <td><?php echo htmlspecialchars($class['student_count']); ?></td>
                        <td>
                            <?php 
                                $startTime = $class['schedule_time_start'] ?? null;
                                echo $startTime
                                    ? date("H:i d-m-Y", strtotime($startTime)) 
                                    : '<span class="text-muted">Chưa có lịch</span>'; 
                            ?>
                        </td>
                        <td>
                            <?php 
                                $endTime = $class['schedule_time_end'] ?? null;
                                echo $endTime 
                                    ? date("H:i d-m-Y", strtotime($endTime)) 
                                    : '<span class="text-muted">Chưa có lịch</span>'; 
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Không tìm thấy lớp học nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../admin/footer.php'; ?>