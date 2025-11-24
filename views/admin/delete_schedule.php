<?php
session_start();
require_once('../../functions/ScheduleFunctions.php');

// Bật báo lỗi MySQLi để debug (nếu cần)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "Không tìm thấy lịch để xóa.";
    header("Location: schedule.php");
    exit;
}

$sFn = new ScheduleFunctions();
$id = intval($_GET['id']);

try {
    $existing = $sFn->getScheduleById($id);

    if (!$existing) {
        $_SESSION['error_message'] = "Lịch không tồn tại.";
        header('Location: schedule.php');
        exit;
    }

    // Kiểm tra quyền: giảng viên chỉ được xóa lịch của mình; admin xóa được tất cả
    $user = $_SESSION['user'];
    if ($user['role'] === 'giangvien' && $existing['user_id'] != $user['user_id']) {
        $_SESSION['error_message'] = "Bạn không có quyền xóa lịch này.";
        header('Location: schedule.php');
        exit;
    }

    $res = $sFn->deleteSchedule($id);
    if ($res) {
        $_SESSION['success_message'] = "Xoá lịch thành công.";
    } else {
        $_SESSION['error_message'] = "Xảy ra lỗi khi xóa lịch.";
    }

} catch (Exception $e) {
    // Bắt tất cả lỗi fatal, ghi vào session để hiển thị
    $_SESSION['error_message'] = "Lỗi hệ thống: " . $e->getMessage();
}

header('Location: schedule.php');
exit;
?>
