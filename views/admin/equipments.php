<?php 
// views/admin/equipments.php
// Đảm bảo các file header, footer, sidebar được include đúng đường dẫn
include 'header.php'; 
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <?php include 'sidebar.php'; ?>
        </div>

        <div class="col-md-9 pt-4"> 
            <h2 class="mb-4">🔧 Quản Lý Thiết Bị</h2>
            
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#equipmentModal">
                <i class="fas fa-plus"></i> Thêm Thiết Bị Mới
            </button>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Danh Sách Thiết Bị & Cơ Sở Vật Chất
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="equipmentsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Thiết Bị</th>
                                    <th>Phòng</th>
                                    <th>Số Lượng</th>
                                    <th>Trạng Thái</th>
                                    <th>Mô Tả</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="equipmentForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="equipmentModalLabel">Thêm Thiết Bị</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="modalAction" value="add">
                    <input type="hidden" name="equipment_id" id="equipmentId">
                    
                    <div class="mb-3">
                        <label for="room_id" class="form-label fw-semibold">Phòng học</label>
                        <select class="form-select" id="roomId" name="room_id" required>
                            <option value="">Chọn Phòng</option>
                            </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="equipment_name" class="form-label fw-semibold">Tên Thiết Bị</label>
                        <input type="text" class="form-control" id="equipmentName" name="equipment_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label fw-semibold">Số Lượng</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Trạng Thái</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Hoạt động">Hoạt động</option>
                            <option value="Hỏng hóc">Hỏng hóc</option>
                            <option value="Bảo trì">Bảo trì</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Mô Tả</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    const handleUrl = '../handles/handle_equipment.php';
    const tableBody = $('#equipmentsTable tbody');
    const equipmentModal = new bootstrap.Modal(document.getElementById('equipmentModal'));

    // Hàm chung để hiển thị thông báo
    function showToast(message, isSuccess) {
        // Tùy chỉnh hàm này để hiển thị thông báo đẹp hơn (ví dụ: Bootstrap Toasts)
        alert(message);
    }

    // 1. Hàm tải danh sách thiết bị
    function loadEquipments() {
        $.ajax({
            url: handleUrl,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    tableBody.empty();
                    if (response.data.length === 0) {
                         tableBody.html('<tr><td colspan="7" class="text-center">Chưa có thiết bị nào được thêm.</td></tr>');
                         return;
                    }
                    response.data.forEach(function(item) {
                        let statusBadge = 'secondary';
                        if (item.status === 'Hoạt động') {
                            statusBadge = 'success';
                        } else if (item.status === 'Hỏng hóc') {
                            statusBadge = 'danger';
                        } else if (item.status === 'Bảo trì') {
                            statusBadge = 'warning';
                        }
                        
                        const row = `
                            <tr>
                                <td>${item.equipment_id}</td>
                                <td>${item.equipment_name}</td>
                                <td>${item.room_name || 'N/A'}</td> 
                                <td>${item.quantity}</td>
                                <td><span class="badge bg-${statusBadge}">${item.status}</span></td>
                                <td>${item.description}</td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-btn" data-id="${item.equipment_id}"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="${item.equipment_id}"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                } else {
                    tableBody.html('<tr><td colspan="7" class="text-center text-danger">Lỗi: ' + response.message + '</td></tr>');
                }
            },
            error: function() {
                showToast('Lỗi kết nối máy chủ.', false);
            }
        });
    }
    
    // 2. Tải danh sách phòng cho Modal (Cần file handle_room.php)
    function loadRoomsForSelect() {
        // *** LƯU Ý: Đảm bảo file handle_room.php có action=get_all để lấy danh sách phòng ***
        $.ajax({
            url: '../handles/handle_room.php', // Giả định handle_room.php tồn tại
            type: 'GET',
            data: { action: 'get_all_simple' }, // Giả định action này trả về ID và Tên phòng
            dataType: 'json',
            success: function(response) {
                const roomSelect = $('#roomId');
                if (response.success && response.data) {
                    roomSelect.empty();
                    roomSelect.append('<option value="">--- Chọn Phòng ---</option>');
                    response.data.forEach(function(room) {
                        roomSelect.append(`<option value="${room.room_id}">${room.room_name}</option>`);
                    });
                }
            }
        });
    }

    // Tải dữ liệu khi trang được tải
    loadEquipments();
    loadRoomsForSelect();

    // 3. Xử lý khi nhấn nút Thêm mới
    $('.btn-primary').on('click', function() {
        $('#equipmentForm')[0].reset();
        $('#modalAction').val('add');
        $('#equipmentModalLabel').text('Thêm Thiết Bị Mới');
        $('#submitBtn').text('Thêm Mới');
        $('#equipmentId').val('');
    });

    // 4. Xử lý khi nhấn nút Sửa
    $(document).on('click', '.edit-btn', function() {
        const equipmentId = $(this).data('id');
        
        $.ajax({
            url: handleUrl,
            type: 'POST',
            data: { action: 'get_details', equipment_id: equipmentId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#equipmentId').val(data.equipment_id);
                    $('#roomId').val(data.room_id);
                    $('#equipmentName').val(data.equipment_name);
                    $('#quantity').val(data.quantity);
                    $('#status').val(data.status);
                    $('#description').val(data.description);
                    
                    $('#modalAction').val('update');
                    $('#equipmentModalLabel').text('Cập Nhật Thiết Bị ID: ' + data.equipment_id);
                    $('#submitBtn').text('Cập Nhật');
                    equipmentModal.show(); // Hiển thị modal
                } else {
                    showToast(response.message, false);
                }
            }
        });
    });

    // 5. Xử lý submit form (Thêm hoặc Sửa)
    $('#equipmentForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: handleUrl,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                showToast(response.message, response.success);
                if (response.success) {
                    equipmentModal.hide();
                    loadEquipments(); // Tải lại bảng
                }
            },
            error: function() {
                showToast('Lỗi kết nối hoặc xử lý yêu cầu.', false);
            }
        });
    });

    // 6. Xử lý khi nhấn nút Xóa
    $(document).on('click', '.delete-btn', function() {
        const equipmentId = $(this).data('id');
        if (confirm('Bạn có chắc chắn muốn xóa thiết bị ID ' + equipmentId + ' này không? Hành động này không thể hoàn tác.')) {
            $.ajax({
                url: handleUrl,
                type: 'POST',
                data: { action: 'delete', equipment_id: equipmentId },
                dataType: 'json',
                success: function(response) {
                    showToast(response.message, response.success);
                    if (response.success) {
                        loadEquipments(); // Tải lại bảng
                    }
                }
            });
        }
    });
});
</script>