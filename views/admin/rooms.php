<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php
require_once '../../functions/RoomFunctions.php';
$roomFn = new RoomFunctions();
$rooms = $roomFn->getAllRooms();
?>
<h3 class="mb-4">🏫 Danh sách phòng học</h3>
<table class="table table-striped table-hover bg-white">
  <thead class="table-info">
    <tr>
      <th>ID</th>
      <th>Tên phòng</th>
      <th>Loại</th>
      <th>Sức chứa</th>
      <th>Thiết bị</th>
      <th>Trạng thái</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rooms as $r): ?>
      <tr>
        <td><?= $r['room_id'] ?></td>
        <td><?= htmlspecialchars($r['room_name']) ?></td>
        <td><?= $r['type'] ?></td>
        <td><?= $r['capacity'] ?></td>
        <td><?= htmlspecialchars($r['equipment']) ?></td>
        <td><?= $r['status'] ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include 'footer.php'; ?>
