<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php
require_once '../../functions/db_connect.php';

// SỬA LỖI: Khởi tạo biến $conn bằng cách gọi hàm connectDB()
$conn = connectDB(); 

$result_rooms = $conn->query("SELECT COUNT(*) as total FROM rooms");
$result_users = $conn->query("SELECT COUNT(*) as total FROM users");
$result_schedules = $conn->query("SELECT COUNT(*) as total FROM schedules");

$total_rooms = $result_rooms->fetch_assoc()['total'];
$total_users = $result_users->fetch_assoc()['total'];
$total_schedules = $result_schedules->fetch_assoc()['total'];
?>
<h2 class="mb-4">📊 Tổng quan hệ thống</h2>
<div class="row">
  <div class="col-md-4">
    <div class="card shadow-sm p-3 text-center">
      <h5>Phòng học</h5>
      <h2 class="text-primary"><?= $total_rooms ?></h2>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm p-3 text-center">
      <h5>Người dùng</h5>
      <h2 class="text-success"><?= $total_users ?></h2>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm p-3 text-center">
      <h5>Lịch học</h5>
      <h2 class="text-danger"><?= $total_schedules ?></h2>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>