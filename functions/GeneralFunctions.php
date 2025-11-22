<?php
// ==============================
//  GENERAL FUNCTIONS (Đã Sửa Lỗi Gán Cứng Subject ID)
// ==============================

// 1. Kiểm tra phòng có bị trùng lịch hay không
function checkRoomAvailability($conn, $room_id, $date, $start_time, $end_time) {
    $start_datetime = date('Y-m-d H:i:s', strtotime("$date $start_time"));
    $end_datetime   = date('Y-m-d H:i:s', strtotime("$date $end_time"));

    $sql = "SELECT COUNT(*) as total_count FROM schedules
            WHERE room_id = ?
              AND NOT (end_time <= ? OR start_time >= ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $room_id, $start_datetime, $end_datetime);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['total_count'] ?? 0;
    $stmt->close();

    return ($count == 0);
}

// 2. Lấy tên phòng
function getRoomNameById($conn, $room_id) {
    $stmt = $conn->prepare("SELECT room_name FROM rooms WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    $stmt->close();
    return $room['room_name'] ?? 'Không rõ';
}

// 3. Lấy lecturer_id từ request
function getLecturerIdFromRequest($conn, $request_id) {
    $stmt = $conn->prepare("SELECT lecturer_id FROM room_requests WHERE request_id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    return $data['lecturer_id'] ?? null;
}

// 4. Lấy tất cả yêu cầu pending
function getPendingRoomRequests($conn) {
    $sql = "SELECT 
                rr.request_id, rr.lecturer_id, rr.room_id, rr.request_date,
                rr.start_time, rr.end_time, rr.purpose, rr.created_at,
                r.room_name,
                u.fullname AS lecturer_name,
                u.user_id AS lecturer_id
            FROM room_requests rr
            JOIN rooms r ON rr.room_id = r.room_id
            JOIN users u ON rr.lecturer_id = u.user_id
            WHERE rr.status = 'pending'
            ORDER BY rr.created_at DESC";

    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// 5. Tạo thông báo cho user
function createNotificationForUser($conn, $receiver_id, $message, $link, $sender_id = 0, $title = "Thông báo") {
    $sql = "INSERT INTO notifications (sender_id, receiver_id, title, message, status, created_at)
            VALUES (?, ?, ?, ?, 'unread', NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $sender_id, $receiver_id, $title, $message);
    $stmt->execute();
    $stmt->close();
}

// 6. Xử lý yêu cầu đặt phòng (approve/reject)
function processRoomRequest($conn, $request_id, $action, $admin_id) {
    // Lấy request pending. ĐÃ BỔ SUNG subject_id VÀ class_id TRONG TRUY VẤN
    $sql_select = "SELECT lecturer_id, room_id, request_date, start_time, end_time, purpose, subject_id, class_id
                   FROM room_requests
                   WHERE request_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql_select);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$request) return false;

    $start_datetime = date('Y-m-d H:i:s', strtotime($request['request_date'].' '.$request['start_time']));
    $end_datetime   = date('Y-m-d H:i:s', strtotime($request['request_date'].' '.$request['end_time']));
    
    $lecturer_id = $request['lecturer_id'];
    $room_id     = $request['room_id'];
    $note        = $request['purpose'];
    
    // SỬA LỖI: Lấy subject_id và class_id chính xác từ request
    $subject_id = $request['subject_id'] ?? 1; 
    $class_id = $request['class_id'] ?? null; // class_id có thể NULL

    if ($action === 'approve') {
        // 1. Cập nhật request
        $sql_update = "UPDATE room_requests
                       SET status='approved', approved_by=?, approved_at=NOW()
                       WHERE request_id=?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ii", $admin_id, $request_id);
        $stmt->execute();
        $stmt->close();

        // 2. Insert vào schedules
        $sql_insert = "INSERT INTO schedules
                       (lecturer_id, room_id, user_id, subject_id, class_id, start_time, end_time, note)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)"; // Bổ sung class_id

        // Chuẩn bị binding types: subject_id và class_id có thể là NULL hoặc INT
        // Giả định class_id có thể là NULL (i), nên ta dùng 'i' cho cả subject_id và class_id
        $stmt2 = $conn->prepare($sql_insert);
        $stmt2->bind_param("iiiisssi", // i: lecturer, room, user, subject; s: class_id, start, end, note (Đã điều chỉnh loại)
            $lecturer_id,
            $room_id,
            $lecturer_id, // user_id = lecturer_id
            $subject_id, 
            $class_id,   
            $start_datetime,
            $end_datetime,
            $note
        );
        $stmt2->execute();
        $stmt2->close();

        // 3. Cập nhật trạng thái phòng
        $sql_room = "UPDATE rooms SET status='dang_su_dung' WHERE room_id=?";
        $stmt3 = $conn->prepare($sql_room);
        $stmt3->bind_param("i", $room_id);
        $stmt3->execute();
        $stmt3->close();

        // 4. Thông báo cho giảng viên
        $msg = "Yêu cầu đặt phòng của bạn (ID: {$request_id}) đã được duyệt.";
        createNotificationForUser($conn, $lecturer_id, $msg, "../views/lecturer/schedule.php", $admin_id);

        return true;
    }

    // REJECT
    if ($action === 'reject') {
        $sql_del = "DELETE FROM room_requests WHERE request_id=?";
        $stmt = $conn->prepare($sql_del);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->close();

        $msg = "Yêu cầu đặt phòng của bạn (ID: {$request_id}) đã bị từ chối.";
        createNotificationForUser($conn, $lecturer_id, $msg, "../views/lecturer/room_lookup.php", $admin_id);

        return true;
    }

    return false;
}
?>
