<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php
require_once '../../functions/ScheduleFunctions.php';
$scheduleFn = new ScheduleFunctions();
$schedules = $scheduleFn->getAllSchedules();
?>
<h3 class="mb-4">📅 Lịch học</h3>
<table class="table table-bordered bg-white table-hover">
  <thead class="table-warning">
    <tr>
      <th>ID</th>
      <th>Phòng</th>
      <th>Giảng viên</th>
      <th>Môn học</th>
      <th>Bắt đầu</th>
      <th>Kết thúc</th>
      <th>Ghi chú</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($schedules as $s): ?>
      <tr>
        <td><?= $s['schedule_id'] ?></td>
        <td><?= $s['room_id'] ?></td>
        <td><?= $s['user_id'] ?></td>
        <td><?= htmlspecialchars($s['subject_name']) ?></td>
        <td><?= $s['start_time'] ?></td>
        <td><?= $s['end_time'] ?></td>
        <td><?= htmlspecialchars($s['note']) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include 'footer.php'; ?>
