<?php 
session_start();
// **Đảm bảo tất cả include và logic xử lý được đặt ở đầu file**
require_once '../../functions/db_connect.php'; 
require_once '../../functions/RoomFunctions.php';
require_once '../../functions/GeneralFunctions.php'; 

// Lấy dữ liệu
$roomFn = new RoomFunctions();
$rooms = $roomFn->getAllRooms();

$conn = connectDB();
$pending_requests = getPendingRoomRequests($conn); // Lấy yêu cầu chờ duyệt (Sử dụng hàm đã sửa lỗi SQL)
closeDB($conn);
?>

<?php include 'header.php'; ?> 
<?php include 'sidebar.php'; ?> 

<div class="container-fluid px-4 py-4">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
    <?php unset($_SESSION['success_message']); endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error_message']; ?></div>
    <?php unset($_SESSION['error_message']); endif; ?>

    
    <h3 class="fw-bold text-primary mb-3">📝 Yêu cầu đặt phòng đang chờ duyệt</h3>
    
    <div class="card shadow-sm border-0 rounded-4 mb-5">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-danger">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Giảng viên</th>
                        <th>Phòng</th>
                        <th>Ngày</th>
                        <th>Thời gian</th>
                        <th>Mục đích</th>
                        <th>Thời điểm gửi</th>
                        <th class="text-center pe-3">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pending_requests)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Không có yêu cầu đặt phòng nào đang chờ duyệt.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pending_requests as $req): ?>
                        <tr>
                            <td class="ps-3"><?= $req['request_id'] ?></td>
                            <td><?= htmlspecialchars($req['lecturer_name']) ?> (ID: <?= $req['lecturer_id'] ?>)</td>
                            <td class="fw-semibold"><?= htmlspecialchars($req['room_name']) ?></td>
                            <td><?= date('d/m/Y', strtotime($req['request_date'])) ?></td>
                            <td><?= date('H:i', strtotime($req['start_time'])) ?> - <?= date('H:i', strtotime($req['end_time'])) ?></td>
                            <td><?= htmlspecialchars($req['purpose']) ?></td>
                            <td><?= date('H:i d/m/Y', strtotime($req['created_at'])) ?></td>
                            <td class="text-center pe-3">
                                <form action="../../handles/handle_admin_request.php" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn DUYỆT yêu cầu ID <?= $req['request_id'] ?>?');">
                                    <input type="hidden" name="action" value="approve_request">
                                    <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success me-1">Duyệt</button>
                                </form>
                                <form action="../../handles/handle_admin_request.php" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn TỪ CHỐI yêu cầu ID <?= $req['request_id'] ?>? Yêu cầu sẽ bị xóa.');">
                                    <input type="hidden" name="action" value="reject_request">
                                    <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Từ chối</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary mb-0">🏫 Danh sách phòng học</h3>
        <div class="d-flex gap-2">
            <input type="text" id="searchRoom" class="form-control" placeholder="🔍 Tìm kiếm phòng..." style="width: 250px;">
            <select id="filterStatus" class="form-select" style="width: 180px;">
                <option value="">Tất cả trạng thái</option>
                <option value="trong">Trống</option>
                <option value="bao_tri">Bảo trì</option>
                <option value="dang_su_dung">Đang sử dụng</option>
            </select>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <table id="roomTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>🏠 Tên phòng</th>
                        <th>📘 Loại</th>
                        <th>👥 Sức chứa</th>
                        <th>📊 Trạng thái</th>
                        <th class="text-center pe-3">⚙️ Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $r): ?>
                        <tr>
                            <td class="ps-3"><?= $r['room_id'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($r['room_name']) ?></td>
                            <td><?= htmlspecialchars($r['type']) ?></td>
                            <td><?= htmlspecialchars($r['capacity']) ?></td>
                            <td>
                                <?php if ($r['status'] == 'trong'): ?>
                                    <span class="badge bg-success">Trống</span>
                                <?php elseif ($r['status'] == 'bao_tri'): ?>
                                    <span class="badge bg-warning text-dark">Bảo trì</span>
                                <?php elseif ($r['status'] == 'dang_su_dung'): ?>
                                    <span class="badge bg-info text-dark">Đang sử dụng</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-3">
                                <button class="btn btn-sm btn-outline-primary" title="Sửa"><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash3"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Đặt JS xuống dưới cùng để tránh lỗi không tìm thấy element
const searchInput = document.getElementById('searchRoom');
const statusFilter = document.getElementById('filterStatus');
const rows = document.querySelectorAll('#roomTable tbody tr');

function filterTable() {
    const searchVal = searchInput.value.toLowerCase();
    // Lấy value (trong, bao_tri, dang_su_dung)
    const statusVal = statusFilter.value; 

    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        const type = row.cells[2].textContent.toLowerCase();
        
        // Lấy nội dung TEXT hiển thị (Trống, Bảo trì, Đang sử dụng)
        const statusText = row.cells[4].textContent.toLowerCase(); 

        const matchesSearch = name.includes(searchVal) || type.includes(searchVal);
        
        // So sánh giá trị hiển thị (statusText) với giá trị đã chọn (statusVal), 
        // vì trong rooms.php của bạn đã ánh xạ đúng (trong -> Trống)
        let matchesStatus = !statusVal; // Nếu không chọn filter, luôn đúng

        if (statusVal === 'trong' && statusText.includes('trống')) {
            matchesStatus = true;
        } else if (statusVal === 'bao_tri' && statusText.includes('bảo trì')) {
            matchesStatus = true;
        } else if (statusVal === 'dang_su_dung' && statusText.includes('đang sử dụng')) {
            matchesStatus = true;
        }
        
        if (matchesSearch && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

searchInput.addEventListener('input', filterTable);
statusFilter.addEventListener('change', filterTable);
</script>

<style>
/* CSS Tùy chỉnh */
body {
    background: #f5f7fa;
}
.table-hover tbody tr:hover {
    background-color: #f0f8ff !important;
    transition: all 0.2s ease;
}
.card {
    transition: box-shadow 0.3s ease;
}
</style>

<?php include 'footer.php'; ?>
