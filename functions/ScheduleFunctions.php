<?php
require_once __DIR__ . '/db_connect.php';

class ScheduleFunctions {
    private $conn;

    public function __construct() {
        $this->conn = connectDB();
    }

    // Lấy tất cả lịch (cho giảng viên hoặc admin), join phòng, môn, lớp
    public function getAllSchedules() {
        $sql = "SELECT sc.schedule_id, sc.room_id, sc.user_id, sc.subject_id, sc.class_id,
                       DATE(sc.start_time) AS date,
                       TIME(sc.start_time) AS start_time,
                       TIME(sc.end_time) AS end_time,
                       sc.note,
                       r.room_name,
                       s.subject_name,
                       c.class_name,
                       u.fullname AS lecturer_name
                FROM schedules sc
                LEFT JOIN rooms r ON sc.room_id = r.room_id
                LEFT JOIN subjects s ON sc.subject_id = s.subject_id
                LEFT JOIN classes c ON sc.class_id = c.class_id
                LEFT JOIN users u ON sc.user_id = u.user_id
                ORDER BY sc.start_time DESC";
        $res = $this->conn->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Kiểm tra trùng lịch (room)
    private function isConflict($room_id, $start_time, $end_time, $exclude_schedule_id = null) {
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

    // Thêm lịch
    public function addSchedule($room_id, $user_id, $subject_id, $class_id, $start_time, $end_time, $note = null) {
        if ($this->isConflict($room_id, $start_time, $end_time)) return "conflict";
        $stmt = $this->conn->prepare("INSERT INTO schedules (room_id, user_id, subject_id, class_id, start_time, end_time, note) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiisss", $room_id, $user_id, $subject_id, $class_id, $start_time, $end_time, $note);
        return $stmt->execute();
    }

    // Cập nhật lịch
    public function updateSchedule($schedule_id, $room_id, $user_id, $subject_id, $class_id, $start_time, $end_time, $note = null) {
        if ($this->isConflict($room_id, $start_time, $end_time, $schedule_id)) return "conflict";
        $stmt = $this->conn->prepare("UPDATE schedules SET room_id=?, user_id=?, subject_id=?, class_id=?, start_time=?, end_time=?, note=? WHERE schedule_id=?");
        $stmt->bind_param("iiiissi", $room_id, $user_id, $subject_id, $class_id, $start_time, $end_time, $note, $schedule_id);
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

    // ===============================================
    // Dành cho giảng viên (lecturer)
    // ===============================================

    // Lấy tất cả lớp
    public function getAllClasses() {
        $res = $this->conn->query("SELECT class_id, class_name FROM classes ORDER BY class_name ASC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Lấy môn học theo giảng viên
    public function getSubjectsByLecturerId($lecturer_id) {
        $stmt = $this->conn->prepare("SELECT subject_id, subject_name FROM subjects WHERE lecturer_id = ? ORDER BY subject_name ASC");
        $stmt->bind_param("i", $lecturer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Đếm số lịch dạy theo ngày
    public function countSchedulesByLecturerAndDate($lecturer_id, $date) {
        $stmt = $this->conn->prepare("SELECT COUNT(schedule_id) FROM schedules WHERE user_id=? AND DATE(start_time)=?");
        $stmt->bind_param("is", $lecturer_id, $date);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    // Lấy lịch theo ngày
    public function getSchedulesByLecturerAndDate($lecturer_id, $date) {
        $stmt = $this->conn->prepare("
            SELECT sc.schedule_id, TIME(sc.start_time) AS start_time, TIME(sc.end_time) AS end_time, r.room_name, s.subject_name
            FROM schedules sc
            LEFT JOIN rooms r ON sc.room_id = r.room_id
            LEFT JOIN subjects s ON sc.subject_id = s.subject_id
            WHERE sc.user_id=? AND DATE(sc.start_time)=?
            ORDER BY sc.start_time
        ");
        $stmt->bind_param("is", $lecturer_id, $date);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy phòng đang dạy hoặc ca tiếp theo
    public function getCurrentTeachingRoom($lecturer_id) {
        $now = date('Y-m-d H:i:s');

        $stmt = $this->conn->prepare("
            SELECT r.room_name
            FROM schedules sc
            LEFT JOIN rooms r ON sc.room_id = r.room_id
            WHERE sc.user_id=? AND sc.start_time<=? AND sc.end_time>?
            LIMIT 1
        ");
        $stmt->bind_param("iss", $lecturer_id, $now, $now);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) return $res->fetch_assoc();

        $stmt2 = $this->conn->prepare("
            SELECT r.room_name
            FROM schedules sc
            LEFT JOIN rooms r ON sc.room_id = r.room_id
            WHERE sc.user_id=? AND sc.start_time>?
            ORDER BY sc.start_time ASC
            LIMIT 1
        ");
        $stmt2->bind_param("is", $lecturer_id, $now);
        $stmt2->execute();
        return $stmt2->get_result()->fetch_assoc();
    }

    public function __destruct() {
        // nếu muốn: $this->conn->close();
    }
}
?>
