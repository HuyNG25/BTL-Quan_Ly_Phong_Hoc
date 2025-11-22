<?php
// Cần đảm bảo đường dẫn này đúng với vị trí của db_connect.php và UserFunctions.php
require_once __DIR__ . '/db_connect.php'; 
// require_once __DIR__ . '/UserFunctions.php'; // Cần file này nếu dùng getCreatorFullname

class NotificationFunctions {
    private $conn;

    public function __construct() {
        $this->conn = connectDB();
    }

    public function getAllNotifications() {
        $sql = "SELECT * FROM notifications ORDER BY created_at DESC";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getNotificationById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM notifications WHERE noti_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    // Hàm lấy tên người tạo (sender_id)
    public function getCreatorFullname($user_id) {
         // TẠM THỜI: Dùng truy vấn trực tiếp vào bảng users
         $stmt = $this->conn->prepare("SELECT fullname FROM users WHERE user_id = ?");
         $stmt->bind_param("i", $user_id);
         $stmt->execute();
         $res = $stmt->get_result()->fetch_assoc();
         return $res['fullname'] ?? 'Admin hệ thống';
    }

    public function addNotification($title, $content, $created_by) {
        $stmt = $this->conn->prepare("INSERT INTO notifications (title, message, sender_id, status) VALUES (?, ?, ?, 'unread')");
        $stmt->bind_param("ssi", $title, $content, $created_by);
        return $stmt->execute();
    }

    public function deleteNotification($id) {
        $stmt = $this->conn->prepare("DELETE FROM notifications WHERE noti_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // ===============================================
    // BỔ SUNG: Dùng cho Dashboard Giảng viên (LECTURER)
    // ===============================================
    
    // Hàm 6: Lấy N thông báo mới nhất (dùng cho Dashboard)
    public function getLatestNotifications($limit = 3) {
        $sql = "SELECT noti_id, title, created_at FROM notifications 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // Hàm 7: Đếm số thông báo chưa đọc của 1 User (Dashboard Sidebar)
    public function countUnreadNotifications($user_id) {
        $sql = "SELECT COUNT(noti_id) FROM user_notifications 
                WHERE user_id = ? AND is_read = FALSE";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        // Kiểm tra xem fetch_column có được hỗ trợ hay không, nếu không dùng fetch_assoc
        if ($result) {
            $row = $result->fetch_array();
            return $row[0] ?? 0;
        }
        return 0;
    }

    // Hàm 8: Đánh dấu thông báo đã đọc
    public function markAsRead($user_id, $noti_id) {
        $sql = "UPDATE user_notifications 
                SET is_read = TRUE, read_at = NOW() 
                WHERE user_id = ? AND noti_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $noti_id);
        return $stmt->execute();
    }
    
    // Hàm 9: Ghi nhận thông báo cho user (khi admin tạo thông báo)
    public function distributeNotificationToAllUsers($noti_id) {
        $sql_insert = "INSERT INTO user_notifications (user_id, noti_id)
                       SELECT user_id, ? FROM users WHERE role IN ('giangvien', 'admin')
                       ON DUPLICATE KEY UPDATE noti_id = VALUES(noti_id)"; 
                       // ON DUPLICATE KEY UPDATE: tránh lỗi nếu UNIQUE KEY (user_id, noti_id) đã tồn tại

        $stmt = $this->conn->prepare($sql_insert);
        $stmt->bind_param("i", $noti_id);
        return $stmt->execute();
    }
    // ===============================================
    // KẾT THÚC BỔ SUNG
    // ===============================================
    // Đóng kết nối khi hủy đối tượng
    public function __destruct() {
        closeDB($this->conn);
    }

}
?>
