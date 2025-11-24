<?php
// functions/LecturerFunctions.php

// ---------------------------------------------------
// 1. Lấy lịch dạy cá nhân (từ room_bookings)
// ---------------------------------------------------
function getAllSchedule($conn, $lecturer_id) {
    $sql = "SELECT 
                b.schedule_id, b.date, b.start_time, b.end_time,
                r.room_name,
                sub.subject_name,
                c.class_name
            FROM room_bookings b
            LEFT JOIN rooms r ON b.room_id = r.room_id
            LEFT JOIN subjects sub ON b.subject_id = sub.subject_id
            LEFT JOIN classes c ON b.class_id = c.class_id
            WHERE b.lecturer_id = ? AND b.status = 'approved'
            ORDER BY b.date ASC, b.start_time ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lecturer_id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ---------------------------------------------------
// 2. Gửi yêu cầu đặt phòng mới
// FIX LỖI: Sửa chuỗi bind_param để khớp chính xác với 8 tham số.
// ---------------------------------------------------
function requestRoomBooking($conn, $lecturer_id, $room_id, $date, $full_start_time, $full_end_time, $purpose, $subject_id) {
    $status = 'pending';

    $sql = "INSERT INTO room_requests 
            (lecturer_id, room_id, request_date, start_time, end_time, purpose, status, subject_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    // ĐÃ SỬA: "iisssssi" - Đảm bảo start_time và end_time là 's'
    // lecturer_id(i), room_id(i), request_date(s), start_time(s), end_time(s), purpose(s), status(s), subject_id(i)
    if (!$stmt->bind_param("iisssssi", $lecturer_id, $room_id, $date, $full_start_time, $full_end_time, $purpose, $status, $subject_id)) {
        // Có thể thêm log lỗi bind_param tại đây nếu cần
        return false; 
    }

    return $stmt->execute();
}

// ---------------------------------------------------
// 3. Lấy số lượng thông báo chưa đọc
// ---------------------------------------------------
function getUnreadNotificationsCount($conn, $user_id) {
    $sql = "SELECT COUNT(noti_id) as cnt
            FROM user_notifications 
            WHERE user_id = ? AND is_read = FALSE";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();
    return $result['cnt'] ?? 0;
}

// ---------------------------------------------------
// 4. Cập nhật hồ sơ giảng viên
// ---------------------------------------------------
function updateLecturerProfile($conn, $user_id, $fullname, $email, $phone, $password_hash = null) {
    if ($password_hash) {
        $sql = "UPDATE users SET fullname=?, email=?, phone=?, password=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $fullname, $email, $phone, $password_hash, $user_id);
    } else {
        $sql = "UPDATE users SET fullname=?, email=?, phone=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $fullname, $email, $phone, $user_id);
    }
    return $stmt->execute();
}

// ---------------------------------------------------
// 5. Lấy tất cả phòng (basic)
// ---------------------------------------------------
function getAllRooms($conn) {
    $sql = "SELECT room_id, room_name, capacity, equipment FROM rooms ORDER BY room_name ASC";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

// ---------------------------------------------------
// 6. Lấy tất cả lớp
// ---------------------------------------------------
function getAllClasses($conn) {
    $sql = "SELECT class_id, class_name FROM classes ORDER BY class_name ASC";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

// ---------------------------------------------------
// 7. Lấy tất cả môn học
// ---------------------------------------------------
function getAllSubjects($conn) {
    $sql = "SELECT subject_id, subject_name FROM subjects ORDER BY subject_name ASC";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

// ---------------------------------------------------
// 8. Lấy chi tiết phòng
// ---------------------------------------------------
function getAllRoomsDetailed($conn) {
    $sql = "SELECT room_id, room_name, type, capacity, equipment, status FROM rooms ORDER BY room_id DESC";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

// ---------------------------------------------------
// 9. Lấy danh sách giảng viên
// ---------------------------------------------------
function getAllLecturers($conn) {
    $sql = "SELECT user_id, fullname, email, phone FROM users WHERE role = 'lecturer' ORDER BY fullname ASC";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

// ---------------------------------------------------
// 10. Lấy chi tiết giảng viên
// ---------------------------------------------------
function getLecturerById($conn, $lecturer_id) {
    $sql = "SELECT user_id, fullname, email, phone FROM users WHERE user_id = ? AND role = 'lecturer'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lecturer_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// ---------------------------------------------------
// 11. Lấy tất cả yêu cầu đặt phòng của giảng viên
// ---------------------------------------------------
function getRoomRequestsByLecturer($conn, $lecturer_id) {
    $sql = "SELECT rr.request_id, rr.room_id, r.room_name, rr.request_date, 
            rr.start_time, rr.end_time, rr.purpose, rr.status
            FROM room_requests rr
            JOIN rooms r ON rr.room_id = r.room_id
            WHERE rr.lecturer_id = ?
            ORDER BY rr.request_date DESC, rr.start_time DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lecturer_id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


// ---------------------------------------------------
// 13. Đánh dấu thông báo đã đọc
// ---------------------------------------------------
function markAllNotificationsAsRead($conn, $user_id) {
    $sql = "UPDATE user_notifications SET is_read = TRUE WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}

// ---------------------------------------------------
// 14. Lấy tất cả thông báo
// ---------------------------------------------------
function getAllNotifications($conn, $user_id) {
    $sql = "SELECT noti_id, message, created_at, is_read 
            FROM user_notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
