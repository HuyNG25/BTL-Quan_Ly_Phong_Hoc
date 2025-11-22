<div class="list-group list-group-flush mt-3 shadow-sm rounded-3 overflow-hidden">
  <a href="dashboard_admin.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 fw-semibold border-0">
    <span class="me-3 fs-5 text-primary">🏠</span> Trang chủ
  </a>
  <a href="profile_admin.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 fw-semibold border-0">
    <span class="me-3 fs-5 text-info">👤</span> Thông tin tài khoản
  </a>
  <a href="users.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 fw-semibold border-0">
    <span class="me-3 fs-5 text-success">👥</span> Người dùng
  </a>
  <a href="rooms.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 fw-semibold border-0">
    <span class="me-3 fs-5 text-warning">🏫</span> Phòng học
  </a>
  <a href="subjects.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 fw-semibold border-0">
    <span class="me-3 fs-5 text-danger">📘</span> Môn học
  </a>
  <a href="schedule.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 fw-semibold border-0">
    <span class="me-3 fs-5 text-secondary">📅</span> Lịch học
  </a>
  <a href="report_admin.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 fw-semibold border-0">
    <span class="me-3 fs-5 text-success">📈</span> Báo cáo & Thống kê
  </a>
  <a href="notifications_admin.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 fw-semibold border-0">
    <span class="me-3 fs-5 text-danger">🔔</span> Thông báo hệ thống
  </a>
</div>

<style>
.list-group-item {
  background-color: #fff;
  transition: all 0.2s ease-in-out;
}
.list-group-item:hover {
  background-color: #f1f5ff;
  transform: translateX(5px);
  border-left: 4px solid #0d6efd;
}
.list-group-item.active {
  background-color: #0d6efd;
  color: #fff !important;
  border-left: 4px solid #0a58ca;
}
.list-group-item.active span {
  filter: brightness(0) invert(1);
}
</style>
