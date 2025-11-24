<?php
// Tên file: handles/handle_room.php (ĐÃ CẬP NHẬT)

session_start();
require_once __DIR__ . '/../functions/RoomFunctions.php';

// Optionally check permission: admin only or admin+staff
if (!isset($_SESSION['user'])) {
    // Nếu đây là yêu cầu AJAX, trả về lỗi JSON thay vì redirect
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Phiên làm việc đã hết hạn.']);
        exit;
    }
    header("Location: ../login.php");
    exit;
}

$roomFn = new RoomFunctions();

// Xử lý yêu cầu GET (Chủ yếu dùng cho AJAX load dữ liệu)
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'get_all_simple') {
        // Trả về danh sách ID và Tên phòng cho Dropdown (AJAX)
        $rooms = $roomFn->getAllRoomsSimple();
        
        header('Content-Type: application/json');
        if (!empty($rooms)) {
            echo json_encode(['success' => true, 'data' => $rooms]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy phòng nào.']);
        }
        exit;
    }
}


// Add (Đã có, giữ nguyên)
if (isset($_POST['add_room'])) {
    $room_name = trim($_POST['room_name']);
    $type = $_POST['type'] ?? 'LT';
    $capacity = intval($_POST['capacity']);
    $equipment = trim($_POST['equipment']);
    $status = $_POST['status'] ?? 'trong';

    if ($roomFn->addRoom($room_name, $type, $capacity, $equipment, $status)) {
        header("Location: ../views/rooms.php?msg=added");
    } else {
        header("Location: ../views/rooms.php?msg=error");
    }
    exit;
}

// Update (Đã có, giữ nguyên)
if (isset($_POST['update_room'])) {
    $id = intval($_POST['room_id']);
    $room_name = trim($_POST['room_name']);
    $type = $_POST['type'] ?? 'LT';
    $capacity = intval($_POST['capacity']);
    $equipment = trim($_POST['equipment']);
    $status = $_POST['status'] ?? 'trong';

    if ($roomFn->updateRoom($id, $room_name, $type, $capacity, $equipment, $status)) {
        header("Location: ../views/rooms.php?msg=updated");
    } else {
        header("Location: ../views/rooms.php?msg=error");
    }
    exit;
}

// Delete (Đã có, giữ nguyên)
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if ($roomFn->deleteRoom($id)) {
        header("Location: ../views/rooms.php?msg=deleted");
    } else {
        header("Location: ../views/rooms.php?msg=error");
    }
    exit;
}
?>
