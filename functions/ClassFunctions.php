<?php
// QUANLYPHONGHOC/functions/ClassFunctions.php
// Không cần require db_connect.php ở đây nếu nó đã được require trong file gọi (classes.php)

class ClassFunctions {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Lấy thông tin chi tiết của một lớp học, bao gồm cả Tên môn học
     */
    public function getClassDetails($classId) {
        $query = "SELECT c.*, s.subject_name 
                  FROM classes c
                  LEFT JOIN subjects s ON c.subject_id = s.subject_id
                  WHERE c.class_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $classId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Lấy tất cả lớp học
     */
    public function getAllClasses() {
        $query = "SELECT c.*, s.subject_name 
                  FROM classes c
                  LEFT JOIN subjects s ON c.subject_id = s.subject_id
                  ORDER BY c.class_name";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Thêm lớp học mới (dùng cho Admin hoặc Giảng viên có quyền)
     */
    public function addClass($className, $studentCount, $subjectId = NULL) {
        $query = "INSERT INTO classes (class_name, student_count, subject_id) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sis", $className, $studentCount, $subjectId);
        return $stmt->execute();
    }
    
    /**
     * HÀM ĐỒNG BỘ: Cập nhật thời gian lên lớp (schedule_time) của lớp
     */
    public function syncClassSchedule($classId) {
        // Tìm lịch học gần nhất hoặc lịch học đầu tiên trong tương lai cho lớp này
        $query = "SELECT start_time, end_time 
                  FROM schedules 
                  WHERE class_id = ? AND start_time >= NOW() 
                  ORDER BY start_time ASC 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $classId);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedule = $result->fetch_assoc();

        if ($schedule) {
            $startTime = $schedule['start_time'];
            $endTime = $schedule['end_time'];
        } else {
            $startTime = NULL;
            $endTime = NULL;
        }

        // Cập nhật vào bảng classes
        $updateQuery = "UPDATE classes 
                        SET schedule_time_start = ?, schedule_time_end = ? 
                        WHERE class_id = ?";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bind_param("ssi", $startTime, $endTime, $classId);
        return $updateStmt->execute();
    }
}
?>