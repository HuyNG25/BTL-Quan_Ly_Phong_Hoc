<?php
require_once __DIR__ . '/db_connect.php';

class ScheduleFunctions {
    private $conn;

    public function __construct() {
        $this->conn = connectDB();
    }

    // Lấy tất cả lịch, join với phòng/môn/giảng viên
    public function getAllSchedules() {
        $sql = "SELECT sc.schedule_id, sc.room_id, sc.user_id, sc.subject_id, sc.start_time, sc.end_time, sc.note,
                       r.room_name, s.subject_name, u.fullname AS lecturer_name
                FROM schedules sc
                LEFT JOIN rooms r ON sc.room_id = r.room_id
                LEFT JOIN subjects s ON sc.subject_id = s.subject_id
                LEFT JOIN users u ON sc.user_id = u.user_id
                ORDER BY sc.start_time DESC";
        $res = $this->conn->query($sql);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    // Kiểm tra trùng lịch (room) — trả về true nếu có conflict
    private function isConflict($room_id, $start_time, $end_time, $exclude_schedule_id = null) {
        // Conflict exists if: existing.start < new.end AND existing.end > new.start
        if ($exclude_schedule_id) {
            $sql = "SELECT COUNT(*) AS cnt FROM schedules WHERE room_id = ? AND schedule_id <> ? AND start_time < ? AND end_time > ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iiss", $room_id, $exclude_schedule_id, $end_time, $start_time);
        } else {
            $sql = "SELECT COUNT(*) AS cnt FROM schedules WHERE room_id = ? AND start_time < ? AND end_time > ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iss", $room_id, $end_time, $start_time);
        }
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return ($res['cnt'] > 0);
    }

    // Thêm lịch: trả về "conflict" nếu trùng, true nếu OK, false nếu lỗi
    public function addSchedule($room_id, $user_id, $subject_id, $start_time, $end_time, $note = null) {
        if ($this->isConflict($room_id, $start_time, $end_time)) {
            return "conflict";
        }
        $stmt = $this->conn->prepare("INSERT INTO schedules (room_id, user_id, subject_id, start_time, end_time, note) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisss", $room_id, $user_id, $subject_id, $start_time, $end_time, $note);
        return $stmt->execute() ? true : false;
    }

    // Cập nhật lịch: tương tự, kiểm tra conflict (loại trừ chính record đang update)
    public function updateSchedule($schedule_id, $room_id, $user_id, $subject_id, $start_time, $end_time, $note = null) {
        if ($this->isConflict($room_id, $start_time, $end_time, $schedule_id)) {
            return "conflict";
        }
        $stmt = $this->conn->prepare("UPDATE schedules SET room_id=?, user_id=?, subject_id=?, start_time=?, end_time=?, note=? WHERE schedule_id=?");
        $stmt->bind_param("iiisssi", $room_id, $user_id, $subject_id, $start_time, $end_time, $note, $schedule_id);
        return $stmt->execute();
    }

    public function deleteSchedule($id) {
        $stmt = $this->conn->prepare("DELETE FROM schedules WHERE schedule_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getScheduleById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM schedules WHERE schedule_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
