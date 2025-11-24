<?php
session_start();
require_once('../../functions/ScheduleFunctions.php');

if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit;
}

$sFn = new ScheduleFunctions();

// Chỉ xử lý POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['update_schedule'])) {
    header('Location: schedule.php');
    exit;
}

$schedule_id = intval($_POST['schedule_id']);
$room_id = intval($_POST['room_id']);
$user_id = intval($_POST['user_id']); // owner của schedule
$subject_id = intval($_POST['subject_id']);
$start_time = trim($_POST['start_time']);
$end_time = trim($_POST['end_time']);
$note = trim($_POST['note'] ?? '');

// Kiểm tra tồn tại lịch
$existing = $sFn->getScheduleById($schedule_id);
if (!$existing) {
    $_SESSION['error_message'] = "Lịch không tồn tại.";
    header('Location: schedule.php');
    exit;
}

// Kiểm tra quyền: nếu user role là 'giangvien' thì chỉ được sửa lịch của chính họ
$user = $_SESSION['user'];
if ($user['role'] === 'giangvien' && $existing['user_id'] != $user['user_id']) {
    $_SESSION['error_message'] = "Bạn không có quyền sửa lịch này.";
    header('Location: schedule.php');
    exit;
}

// Gọi cập nhật
$res = $sFn->updateSchedule($schedule_id, $room_id, $user_id, $subject_id, intval($existing['class_id'] ?? 0), $start_time, $end_time, $note);

if ($res === true) {
    $_SESSION['success_message'] = "Cập nhật lịch thành công.";
    header('Location: schedule.php');
    exit;
} elseif ($res === "conflict") {
    $_SESSION['error_message'] = "Xung đột thời gian với một lịch khác ở cùng phòng.";
    header('Location: schedule.php');
    exit;
} else {
    $_SESSION['error_message'] = "Có lỗi khi cập nhật lịch.";
    header('Location: schedule.php');
    exit;
}
