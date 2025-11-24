<?php
// handles/handle_admin_request.php

session_start();
// Lưu ý: Các đường dẫn include này phải đúng với vị trí của file handle_admin_request.php
require_once('../functions/db_connect.php'); 
require_once('../functions/GeneralFunctions.php'); 
require_once('../functions/RoomFunctions.php'); 
require_once('../functions/LecturerFunctions.php'); 
require_once('../functions/ScheduleFunctions.php'); // Cần thêm nếu bạn sử dụng ScheduleFunctions

if (!isset($_SESSION['user'])) {
    $_SESSION['error_message'] = "Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.";
    header('Location: ../../login.php');
    exit();
}

$conn = connectDB();
$user_id = $_SESSION['user']['user_id'];
$user_role = $_SESSION['user']['role'];

// Đặt URL chuyển hướng mặc định (sẽ được thay đổi tùy theo hành động)
$redirect_url = ($user_role === 'admin') 
    ? '../views/admin/dashboard_admin.php' 
    : '../views/lecturer/dashboard_lecturer.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $action = $_POST['action'] ?? '';

    // ================================================
    // 1. Gửi yêu cầu đặt phòng từ giảng viên
    // ================================================
    if ($action == 'request_booking') {
        $redirect_url = '../views/lecturer/room_lookup.php'; // Chuyển hướng sau xử lý
        
        if ($user_role !== 'giangvien') {
            $_SESSION['error_message'] = "Truy cập bị từ chối. Bạn không có quyền gửi yêu cầu đặt phòng.";
        } else {
            // Lấy subject_id
            $subject_id = filter_var($_POST['subject_id'], FILTER_SANITIZE_NUMBER_INT); 
            $room_id = filter_var($_POST['room_id'], FILTER_SANITIZE_NUMBER_INT);
            $date = $_POST['date']; // Dạng YYYY-MM-DD
            $start_time_only = $_POST['start_time']; // Dạng HH:MM
            $end_time_only = $_POST['end_time']; // Dạng HH:MM
            
            // FIX LỖI 1: Tránh "Undefined array key "purpose"". Sử dụng ?? ''
            $purpose = trim($_POST['purpose'] ?? ''); 
            
            // FIX LỖI 2: Tạo chuỗi DATETIME hoàn chỉnh (YYYY-MM-DD HH:MM:SS)
            $full_start_time = $date . ' ' . $start_time_only . ':00';
            $full_end_time = $date . ' ' . $end_time_only . ':00';
            
            // Lưu ý: hàm checkRoomAvailability phải được sửa để nhận $full_start_time, $full_end_time (nếu nó chưa được sửa)
            if (!checkRoomAvailability($conn, $room_id, $date, $start_time_only, $end_time_only)) { // Giữ nguyên tham số cũ nếu checkRoomAvailability chưa được sửa
                $_SESSION['error_message'] = "Phòng đã có lịch vào thời gian bạn yêu cầu. Vui lòng chọn thời gian khác.";
            // LỆNH GỌI HÀM: Đã truyền đủ 8 tham số.
            } else if (requestRoomBooking($conn, $user_id, $room_id, $date, $full_start_time, $full_end_time, $purpose, $subject_id)) {
                $_SESSION['success_message'] = "Yêu cầu đặt phòng đã được gửi thành công và đang chờ Admin duyệt.";
            } else {
                $_SESSION['error_message'] = "Lỗi hệ thống khi gửi yêu cầu đặt phòng.";
            }
        }
    }

    // ================================================
    // 2. Duyệt/Từ chối request từ admin
    // ================================================
    elseif ($action == 'approve_request' || $action == 'reject_request') {
        // CHUYỂN HƯỚNG ĐÚNG: Admin luôn quay lại trang Admin/rooms.php sau khi xử lý.
        $redirect_url = '../views/admin/rooms.php'; 

        if ($user_role !== 'admin') {
            $_SESSION['error_message'] = "Truy cập bị từ chối. Chỉ Admin mới được duyệt/từ chối yêu cầu.";
        } else {
            $request_id = filter_var($_POST['request_id'], FILTER_SANITIZE_NUMBER_INT);
            $admin_id = $user_id; 
            $command = ($action == 'approve_request') ? 'approve' : 'reject';
            
            if (processRoomRequest($conn, $request_id, $command, $admin_id)) {
                $msg = ($command == 'approve') ? "đã được DUYỆT" : "đã được TỪ CHỐI (và xóa)";
                $_SESSION['success_message'] = "Yêu cầu đặt phòng ID {$request_id} {$msg} thành công.";
            } else {
                $_SESSION['error_message'] = "Lỗi khi xử lý yêu cầu đặt phòng. Có thể yêu cầu đã được xử lý hoặc không tồn tại.";
            }
        }
    }
} 

closeDB($conn);
header('Location: ' . $redirect_url);
exit();
?>
