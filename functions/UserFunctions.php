<?php
require_once __DIR__ . '/db_connect.php';

class UserFunctions {
    private $conn;

    public function __construct() {
        $this->conn = connectDB();
    }

    // Login: trả về user row (assoc) nếu OK, ngược lại false
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($user = $res->fetch_assoc()) {
            // SỬ DỤNG MẬT KHẨU THUẦN TÚY: So sánh trực tiếp
            if ($password === $user['password']) { 
                return $user;
            }
        }
        return false;
    }

    // Thêm người dùng (Lưu mật khẩu Plain Text)
    public function addUser($fullname, $email, $password, $role = 'giangvien') {
        $stmt = $this->conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullname, $email, $password, $role);
        return $stmt->execute();
    }

    // Đổi mật khẩu (Cập nhật mật khẩu Plain Text)
    public function changePassword($id, $newPassword) {
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $newPassword, $id);
        return $stmt->execute();
    }
    
    // Các hàm khác
    public function getAllUsers() {
        $sql = "SELECT user_id, fullname, email, role, created_at FROM users ORDER BY user_id DESC";
        $res = $this->conn->query($sql);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT user_id, fullname, email, role, created_at FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    // Cập nhật thông tin cơ bản (fullname, email, role)
    public function updateUser($id, $fullname, $email, $role) {
        $stmt = $this->conn->prepare("UPDATE users SET fullname = ?, email = ?, role = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $fullname, $email, $role, $id);
        return $stmt->execute();
    }
    
    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
