<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php
require_once '../../functions/SubjectFunctions.php';
$subjectFn = new SubjectFunctions();
$subjects = $subjectFn->getAllSubjects();
?>
<h3 class="mb-4">📘 Quản lý môn học</h3>
<table class="table table-hover bg-white table-bordered">
  <thead class="table-success">
    <tr>
      <th>ID</th>
      <th>Mã môn</th>
      <th>Tên môn</th>
      <th>Tín chỉ</th>
      <th>Giảng viên phụ trách</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($subjects as $s): ?>
      <tr>
        <td><?= $s['subject_id'] ?></td>
        <td><?= $s['subject_code'] ?></td>
        <td><?= htmlspecialchars($s['subject_name']) ?></td>
        <td><?= $s['credits'] ?></td>
        <td><?= $s['lecturer_id'] ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include 'footer.php'; ?>
