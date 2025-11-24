<?php
session_start();
require_once __DIR__ . '/../functions/ScheduleFunctions.php';
require_once __DIR__ . '/../functions/LecturerFunctions.php'; 

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$sFn = new ScheduleFunctions();
// $lFn = new LecturerFunctions(); // Khởi tạo nếu cần dùng các hàm Giảng viên

// Thiết lập URL chuyển hướng mặc định sau khi xử lý (dùng cho POST/DELETE)
$redirect_url = '../views/lecturer/schedule.php'; 
$redirect_delete_success = $redirect_url . '?msg=deleted';
$redirect_delete_error = $redirect_url . '?msg=delete_error';

// ===============================================
// XỬ LÝ GET REQUEST (Thường dùng cho DELETE)
// ===============================================
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action'])) {
    
    $action = $_GET['action'];

    if ($action === 'delete' && isset($_GET['id'])) {
        $schedule_id = intval($_GET['id']);
        
        // Thực hiện xóa lịch
        if ($sFn->deleteSchedule($schedule_id)) {
            // ✅ FIX LỖI: Điều hướng sau khi xóa thành công
            header("Location: " . $redirect_delete_success);
            exit; 
        } else {
            // Điều hướng khi xóa thất bại
            header("Location: " . $redirect_delete_error);
            exit;
        }
    }
}


// ===============================================
// XỬ LÝ POST REQUEST (Add/Update/Request)
// ===============================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // 1. Gửi yêu cầu đặt phòng (Lecturer's request)
    if ($action == 'request_schedule') {
        // Lấy dữ liệu từ form
        $room_id = intval($_POST['room_id']);
        $lecturer_id = $_SESSION['user']['user_id']; 
        
        $lop_hoc = isset($_POST['lop_hoc']) ? htmlspecialchars(trim($_POST['lop_hoc'])) : ''; 
        $mon_hoc = isset($_POST['mon_hoc']) ? htmlspecialchars(trim($_POST['mon_hoc'])) : ''; 
        
        $date = isset($_POST['date']) ? trim($_POST['date']) : '';
        $start_time = isset($_POST['start_time']) ? trim($_POST['start_time']) : '';
        $end_time = isset($_POST['end_time']) ? trim($_POST['end_time']) : '';
        $muc_dich = isset($_POST['muc_dich']) ? htmlspecialchars(trim($_POST['muc_dich'])) : '';

        $res = $sFn->addRequestSchedule($room_id, $lecturer_id, $mon_hoc, $lop_hoc, $muc_dich, $date, $start_time, $end_time);

        if ($res === true) {
            header("Location: ../views/lecturer/room_lookup.php?success=Yêu cầu đặt phòng đã được gửi và đang chờ duyệt.");
        } elseif ($res === "conflict") {
            header("Location: ../views/lecturer/room_lookup.php?error=Phòng đã được đặt trong khoảng thời gian này.");
        } else {
            header("Location: ../views/lecturer/room_lookup.php?error=Có lỗi hệ thống xảy ra khi gửi yêu cầu.");
        }
        exit;
    }

    // 2. Add/Update lịch trực tiếp (Admin/Schedule view)
    
    // Add
    if (isset($_POST['add_schedule'])) {
        $room_id = intval($_POST['room_id']);
        $user_id = intval($_POST['user_id']); 
        $subject_id = intval($_POST['subject_id']); 
        
        // FIX LỖI 1: Lấy class_id từ form và chuẩn hóa NULL
        $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : null;
        if ($class_id === 0) $class_id = null; // Nếu giá trị là 0 thì coi là NULL
        
        $start_time = trim($_POST['start_time']); 
        $end_time = trim($_POST['end_time']);
        $note = trim($_POST['note'] ?? '');

        // FIX LỖI 1: Thêm $class_id vào hàm addSchedule
        $res = $sFn->addSchedule($room_id, $user_id, $subject_id, $class_id, $start_time, $end_time, $note);

        if ($res === true) {
            // FIX LỖI 404: Trỏ đúng đường dẫn tới views/lecturer/schedule.php
            header("Location: ../views/lecturer/schedule.php?msg=added");
        } elseif ($res === "conflict") {
            header("Location: ../views/lecturer/schedule.php?msg=conflict");
        } else {
            header("Location: ../views/lecturer/schedule.php?msg=error");
        }
        exit;
    }

    // Update
    if (isset($_POST['update_schedule'])) {
        $schedule_id = intval($_POST['schedule_id']);
        $room_id = intval($_POST['room_id']);
        $user_id = intval($_POST['user_id']);
        $subject_id = intval($_POST['subject_id']);
        
        // FIX LỖI 1: Lấy class_id từ form và chuẩn hóa NULL
        $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : null;
        if ($class_id === 0) $class_id = null; // Nếu giá trị là 0 thì coi là NULL
        
        $start_time = trim($_POST['start_time']);
        $end_time = trim($_POST['end_time']);
        $note = trim($_POST['note'] ?? '');

        // FIX LỖI 1: Thêm $class_id vào hàm updateSchedule (Đây là nguyên nhân lỗi datetime trước đó)
        $res = $sFn->updateSchedule($schedule_id, $room_id, $user_id, $subject_id, $class_id, $start_time, $end_time, $note);
        
        if ($res === true) {
            // FIX LỖI 404: Trỏ đúng đường dẫn tới views/lecturer/schedule.php
            header("Location: ../views/lecturer/schedule.php?msg=updated");
        } elseif ($res === "conflict") {
            header("Location: ../views/lecturer/schedule.php?msg=conflict");
        } else {
            header("Location: ../views/lecturer/schedule.php?msg=error");
        }
        exit;
    }
}

// Nếu không có POST hoặc GET action nào được xử lý, quay về trang lịch.
header("Location: " . $redirect_url);
exit;
?>
