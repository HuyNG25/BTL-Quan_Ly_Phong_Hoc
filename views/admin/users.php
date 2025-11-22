<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php
require_once '../../functions/UserFunctions.php';
$userFn = new UserFunctions();
$users = $userFn->getAllUsers();
?>

<div class="container-fluid px-4 py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-primary mb-0">👥 Quản lý người dùng</h3>
    <div class="d-flex gap-2">
      <input type="text" id="searchUser" class="form-control" placeholder="🔍 Tìm kiếm người dùng..." style="width: 250px;">
      <select id="filterRole" class="form-select" style="width: 160px;">
        <option value="">Tất cả vai trò</option>
        <option value="admin">Admin</option>
        <option value="giangvien">Giảng viên</option>
      </select>
    </div>
  </div>

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <table id="userTable" class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>👤 Họ tên</th>
            <th>✉️ Email</th>
            <th>🔑 Vai trò</th>
            <th class="text-center">⚙️ Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><?= $u['user_id'] ?></td>
              <td class="fw-semibold"><?= htmlspecialchars($u['fullname']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <?php if ($u['role'] == 'admin'): ?>
                  <span class="badge bg-danger">Admin</span>
                <?php elseif ($u['role'] == 'giangvien'): ?>
                  <span class="badge bg-info text-dark">Giảng viên</span>
                <?php else: ?>
                  <span class="badge bg-success">Sinh viên</span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <button class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></button>
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash3"></i></button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- JS tìm kiếm và lọc -->
<script>
const searchInput = document.getElementById('searchUser');
const roleFilter = document.getElementById('filterRole');
const rows = document.querySelectorAll('#userTable tbody tr');

function filterTable() {
  const searchVal = searchInput.value.toLowerCase();
  const roleVal = roleFilter.value;

  rows.forEach(row => {
    const name = row.cells[1].textContent.toLowerCase();
    const email = row.cells[2].textContent.toLowerCase();
    const role = row.cells[3].textContent.toLowerCase();

    const matchesSearch = name.includes(searchVal) || email.includes(searchVal);
    const matchesRole = !roleVal || role.includes(roleVal);

    if (matchesSearch && matchesRole) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

searchInput.addEventListener('input', filterTable);
roleFilter.addEventListener('change', filterTable);
</script>

<!-- CSS style -->
<style>
body {
  background: #f5f7fa;
}
.table-hover tbody tr:hover {
  background-color: #f0f8ff !important;
  transition: all 0.2s ease;
}
.card {
  transition: box-shadow 0.3s ease, transform 0.3s ease;
}
.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}
</style>

<?php include 'footer.php'; ?>
