<?php
require_once __DIR__ . '/db_connect.php';

class NotificationFunctions {
    private $conn;

    public function __construct() {
        $this->conn = connectDB();
    }

    public function getAllNotifications() {
        $sql = "SELECT * FROM notifications ORDER BY created_at DESC";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function addNotification($title, $content, $created_by) {
        $stmt = $this->conn->prepare("INSERT INTO notifications (title, content, created_by) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $content, $created_by);
        return $stmt->execute();
    }

    public function deleteNotification($id) {
        $stmt = $this->conn->prepare("DELETE FROM notifications WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
