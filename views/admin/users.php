<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php
require_once '../../functions/UserFunctions.php';
$userFn = new UserFunctions();
$users = $userFn->getAllUsers();
?>
<h3 class="mb-4">👥 Quản lý người dùng</h3>
<table class="table table-bordered table-hover bg-white">
  <thead class="table-primary">
    <tr>
      <th>ID</th>
      <th>Họ tên</th>
      <th>Email</th>
      <th>Vai trò</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= $u['user_id'] ?></td>
        <td><?= htmlspecialchars($u['fullname']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= $u['role'] ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include 'footer.php'; ?>
