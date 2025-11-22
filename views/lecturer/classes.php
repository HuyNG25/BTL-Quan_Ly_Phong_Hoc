<?php
session_start();

// Kiểm tra đăng nhập và vai trò
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'giangvien' && $_SESSION['user']['role'] !== 'admin')) {
    header('Location: ../../../login.php'); 
    exit();
}

require_once '../../functions/db_connect.php';
require_once '../../functions/ClassFunctions.php'; 

$conn = connectDB(); 
$ClassFunctions = new ClassFunctions($conn); 
$classes = $ClassFunctions->getAllClasses(); 
closeDB($conn);

include '../admin/header.php'; 
?>

<div class="container my-5">
    <h2 class="text-center text-primary mb-4"><i class="fas fa-chalkboard-teacher"></i> Thông tin cơ bản của các Lớp học</h2>

```
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Mã Lớp</th>
                        <th>Tên Lớp</th>
                        <th>Số Lượng sinh viên</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <tr>
                                <td><?= htmlspecialchars($class['class_id']) ?></td>
                                <td><?= htmlspecialchars($class['class_name']) ?></td>
                                <td><?= htmlspecialchars($class['student_count'] ?? 0) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">Không tìm thấy lớp học nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4 text-center">
    <a href="../../views/lecturer/dashboard_lecturer.php" class="btn btn-primary"><i class="fas fa-home"></i> Trang Chủ</a>
</div>
```

</div>

<!-- FontAwesome & Bootstrap JS -->

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include '../admin/footer.php'; ?>
