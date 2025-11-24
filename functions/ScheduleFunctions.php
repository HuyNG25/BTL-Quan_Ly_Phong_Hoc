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
                        sc.start_time AS full_start_time,
                        sc.end_time AS full_end_time,
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
        $stmt->close();
        return ($res['cnt'] > 0);
    }

    // Thêm lịch (class_id có thể null)
    public function addSchedule($room_id, $user_id, $subject_id, $class_id, $start_time, $end_time, $note = null) {
        if ($this->isConflict($room_id, $start_time, $end_time)) return "conflict";
        
        // Logic để chèn class_id=NULL nếu $class_id là null
        if ($class_id === null) {
            $stmt = $this->conn->prepare("INSERT INTO schedules (room_id, user_id, subject_id, class_id, start_time, end_time, note) VALUES (?, ?, ?, NULL, ?, ?, ?)");
            $stmt->bind_param("iiisss", $room_id, $user_id, $subject_id, $start_time, $end_time, $note);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO schedules (room_id, user_id, subject_id, class_id, start_time, end_time, note) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiisss", $room_id, $user_id, $subject_id, $class_id, $start_time, $end_time, $note);
        }
        
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // Cập nhật lịch (class_id có thể null)
    // FIX LỖI: Cập nhật định nghĩa hàm để nhận $class_id
    public function updateSchedule($schedule_id, $room_id, $user_id, $subject_id, $class_id, $start_time, $end_time, $note = null) {
        if ($this->isConflict($room_id, $start_time, $end_time, $schedule_id)) return "conflict";

        // Logic để chèn class_id=NULL nếu $class_id là null
        if ($class_id === null) {
            $stmt = $this->conn->prepare("UPDATE schedules SET room_id=?, user_id=?, subject_id=?, class_id=NULL, start_time=?, end_time=?, note=? WHERE schedule_id=?");
            $stmt->bind_param("iiisssi", $room_id, $user_id, $subject_id, $start_time, $end_time, $note, $schedule_id);
        } else {
            $stmt = $this->conn->prepare("UPDATE schedules SET room_id=?, user_id=?, subject_id=?, class_id=?, start_time=?, end_time=?, note=? WHERE schedule_id=?");
            $stmt->bind_param("iiiisssi", $room_id, $user_id, $subject_id, $class_id, $start_time, $end_time, $note, $schedule_id);
        }

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function deleteSchedule($id) {
        $stmt = $this->conn->prepare("DELETE FROM schedules WHERE schedule_id = ?");
        $stmt->bind_param("i", $id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function getScheduleById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM schedules WHERE schedule_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res;
    }

    // ===============================================
    // Dành cho giảng viên (lecturer)
    // ===============================================

    public function getAllClasses() {
        $res = $this->conn->query("SELECT class_id, class_name FROM classes ORDER BY class_name ASC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getSubjectsByLecturerId($lecturer_id) {
        $stmt = $this->conn->prepare("SELECT subject_id, subject_name FROM subjects WHERE lecturer_id = ? ORDER BY subject_name ASC");
        $stmt->bind_param("i", $lecturer_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }

    public function countSchedulesByLecturerAndDate($lecturer_id, $date) {
        $stmt = $this->conn->prepare("SELECT COUNT(schedule_id) FROM schedules WHERE user_id=? AND DATE(start_time)=?");
        $stmt->bind_param("is", $lecturer_id, $date);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

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
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }

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
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $stmt->close();
            return $row;
        }
        $stmt->close();

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
        $res2 = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();
        return $res2;
    }

    public function __destruct() {
        // $this->conn->close(); // tuỳ muốn đóng
    }
}
?>
