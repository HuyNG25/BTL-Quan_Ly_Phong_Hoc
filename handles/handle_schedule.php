<?php
session_start();
require_once __DIR__ . '/../functions/ScheduleFunctions.php';
// Yêu cầu thêm LecturerFunctions.php nếu cần dùng hàm getSubjectsByLecturerId
require_once __DIR__ . '/../functions/LecturerFunctions.php'; 

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$sFn = new ScheduleFunctions();
// $lFn = new LecturerFunctions(); // Khởi tạo nếu cần dùng các hàm Giảng viên

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // ===============================================
    // 1. Gửi yêu cầu đặt phòng (Lecturer's request)
    // ===============================================
    if ($action == 'request_schedule') {
        // Lấy dữ liệu từ form
        $room_id = intval($_POST['room_id']);
        $lecturer_id = $_SESSION['user']['user_id']; // Giả định lecturer_id được lấy từ session
        
        // BƯỚC SỬA 3: LẤY THÊM BIẾN LỚP HỌC MỚI
        $lop_hoc = isset($_POST['lop_hoc']) ? htmlspecialchars(trim($_POST['lop_hoc'])) : ''; 
        
        // BƯỚC SỬA 4: BIẾN MÔN HỌC PHẢI LẤY TRỰC TIẾP TỪ FORM (KHẮC PHỤC LỖI SAI LỆCH)
        // Giá trị này phải là TÊN môn học, e.g., 'Tiếng Anh'
        $mon_hoc = isset($_POST['mon_hoc']) ? htmlspecialchars(trim($_POST['mon_hoc'])) : ''; 
        
        $date = isset($_POST['date']) ? trim($_POST['date']) : '';
        $start_time = isset($_POST['start_time']) ? trim($_POST['start_time']) : '';
        $end_time = isset($_POST['end_time']) ? trim($_POST['end_time']) : '';
        $muc_dich = isset($_POST['muc_dich']) ? htmlspecialchars(trim($_POST['muc_dich'])) : '';


        // Kiểm tra tính hợp lệ của dữ liệu (nên có thêm ở đây)

        // BƯỚC SỬA 5: THÊM $lop_hoc VÀO HÀM GỌI addRequestSchedule
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

    // ===============================================
    // 2. Add/Update lịch trực tiếp (Admin/Schedule view)
    // ===============================================
    // (Giữ nguyên các đoạn code xử lý add_schedule, update_schedule, delete_schedule)
    
    // Add
    if (isset($_POST['add_schedule'])) {
        $room_id = intval($_POST['room_id']);
        $user_id = intval($_POST['user_id']); 
        $subject_id = intval($_POST['subject_id']); // Đây là subject_id (ID Môn học)
        $start_time = trim($_POST['start_time']); 
        $end_time = trim($_POST['end_time']);
        $note = trim($_POST['note'] ?? '');

        // ... (Xử lý $class_id nếu có)

        // Giả định hàm addSchedule trong ScheduleFunctions nhận $subject_id (ID)
        $res = $sFn->addSchedule($room_id, $user_id, $subject_id, $start_time, $end_time, $note);

        if ($res === true) {
            header("Location: ../views/schedule.php?msg=added");
        } elseif ($res === "conflict") {
            header("Location: ../views/schedule.php?msg=conflict");
        } else {
            header("Location: ../views/schedule.php?msg=error");
        }
        exit;
    }

    // Update
    if (isset($_POST['update_schedule'])) {
        $schedule_id = intval($_POST['schedule_id']);
        $room_id = intval($_POST['room_id']);
        $user_id = intval($_POST['user_id']);
        $subject_id = intval($_POST['subject_id']);
        $start_time = trim($_POST['start_time']);
        $end_time = trim($_POST['end_time']);
        $note = trim($_POST['note'] ?? '');

        $res = $sFn->updateSchedule($schedule_id, $room_id, $user_id, $subject_id, $start_time, $end_time, $note);
        if ($res === true) {
            header("Location: ../views/schedule.php?msg=updated");
        } elseif ($res === "conflict") {
            header("Location: ../views/schedule.php?msg=conflict");
        } else {
            header("Location: ../views/schedule.php?msg=error");
        }
        exit;
    }
}
