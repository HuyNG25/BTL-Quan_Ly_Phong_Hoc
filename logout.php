<?php
session_start();

// Hủy tất cả các biến session
$_SESSION = array();

// Nếu muốn xóa session cookie, xóa cả session cookie.
// Lưu ý: Thao tác này sẽ không có hiệu lực cho đến khi trang được tải lại.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hủy session
session_destroy();

// Chuyển hướng đến trang đăng nhập (giả sử là login.php nằm ở thư mục gốc)
header("Location: login.php");
exit;
?>
