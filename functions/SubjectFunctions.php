<?php
require_once __DIR__ . '/db_connect.php';

class SubjectFunctions {
    private $conn;

    public function __construct() {
        $this->conn = connectDB();
    }

    public function getAllSubjects() {
        $sql = "SELECT s.subject_id, s.subject_code, s.subject_name, s.credits, s.lecturer_id, u.fullname AS lecturer_name
                FROM subjects s
                LEFT JOIN users u ON s.lecturer_id = u.user_id
                ORDER BY s.subject_id DESC";
        $res = $this->conn->query($sql);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function addSubject($subject_code, $subject_name, $credits, $lecturer_id = null) {
        $stmt = $this->conn->prepare("INSERT INTO subjects (subject_code, subject_name, credits, lecturer_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $subject_code, $subject_name, $credits, $lecturer_id);
        return $stmt->execute();
    }

    public function getSubjectById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM subjects WHERE subject_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateSubject($id, $subject_code, $subject_name, $credits, $lecturer_id = null) {
        $stmt = $this->conn->prepare("UPDATE subjects SET subject_code=?, subject_name=?, credits=?, lecturer_id=? WHERE subject_id=?");
        $stmt->bind_param("ssiii", $subject_code, $subject_name, $credits, $lecturer_id, $id);
        return $stmt->execute();
    }

    public function deleteSubject($id) {
        $stmt = $this->conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
