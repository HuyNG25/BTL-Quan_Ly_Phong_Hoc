<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php
require_once '../../functions/SubjectFunctions.php';
$subjectFn = new SubjectFunctions();
$subjects = $subjectFn->getAllSubjects();
?>

<div class="container-fluid px-4 py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-success mb-0">📘 Danh sách môn học</h3>
    <div class="d-flex gap-2">
      <input type="text" id="searchSubject" class="form-control shadow-sm" placeholder="🔍 Tìm kiếm môn học..." style="width: 260px;">
      <select id="filterCredits" class="form-select shadow-sm" style="width: 150px;">
        <option value="">Tất cả tín chỉ</option>
        <option value="2">2 tín chỉ</option>
        <option value="3">3 tín chỉ</option>
        <option value="4">4 tín chỉ</option>
      </select>
    </div>
  </div>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
      <table id="subjectTable" class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>📗 Mã môn</th>
            <th>Tên môn</th>
            <th>🎓 Tín chỉ</th>
            <th>👨‍🏫 Giảng viên phụ trách</th>
            <th class="text-center">⚙️ Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($subjects as $s): ?>
            <tr>
              <td><?= $s['subject_id'] ?></td>
              <td class="fw-semibold text-primary"><?= htmlspecialchars($s['subject_code']) ?></td>
              <td><?= htmlspecialchars($s['subject_name']) ?></td>
              <td><span class="badge bg-success"><?= $s['credits'] ?></span></td>
              <td><?= htmlspecialchars($s['lecturer_name'] ?? 'Chưa phân công') ?></td>
              <td class="text-center">
                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></button>
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i></button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
const searchInput = document.getElementById('searchSubject');
const creditFilter = document.getElementById('filterCredits');
const rows = document.querySelectorAll('#subjectTable tbody tr');

function filterTable() {
  const searchVal = searchInput.value.toLowerCase();
  const creditVal = creditFilter.value;

  rows.forEach(row => {
    const code = row.cells[1].textContent.toLowerCase();
    const name = row.cells[2].textContent.toLowerCase();
    const credit = row.cells[3].textContent.trim();

    const matchSearch = code.includes(searchVal) || name.includes(searchVal);
    const matchCredit = !creditVal || credit === creditVal;

    row.style.display = matchSearch && matchCredit ? '' : 'none';
  });
}

searchInput.addEventListener('input', filterTable);
creditFilter.addEventListener('change', filterTable);
</script>

<style>
body {
  background: #f8fafc;
}
.table-hover tbody tr:hover {
  background-color: #f0fff4 !important;
  transition: all 0.2s ease;
}
.card {
  transition: 0.3s ease;
}
.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
.badge {
  font-size: 0.85rem;
  padding: 6px 10px;
}
</style>

<?php include 'footer.php'; ?>
