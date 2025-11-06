<?php
require_once __DIR__ . '/db_connect.php';

class TimetableFunctions {
    private $conn;

    public function __construct() {
        $this->conn = connectDB();
    }

    public function getTimetable($from = null, $to = null, $room_id = null, $lecturer_id = null) {
        $sql = "SELECT sc.schedule_id, sc.start_time, sc.end_time, r.room_name, s.subject_name, u.fullname AS lecturer_name
                FROM schedules sc
                JOIN rooms r ON sc.room_id = r.room_id
                JOIN subjects s ON sc.subject_id = s.subject_id
                JOIN users u ON sc.user_id = u.user_id
                WHERE 1=1";
        $params = [];
        $types = "";

        if ($from) { $sql .= " AND sc.start_time >= ?"; $params[] = $from; $types .= "s"; }
        if ($to) { $sql .= " AND sc.end_time <= ?"; $params[] = $to; $types .= "s"; }
        if ($room_id) { $sql .= " AND sc.room_id = ?"; $params[] = $room_id; $types .= "i"; }
        if ($lecturer_id) { $sql .= " AND sc.user_id = ?"; $params[] = $lecturer_id; $types .= "i"; }

        $sql .= " ORDER BY sc.start_time ASC";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
