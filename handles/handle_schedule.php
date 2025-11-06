<?php
session_start();
require_once __DIR__ . '/../functions/ScheduleFunctions.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$sFn = new ScheduleFunctions();

// Add
if (isset($_POST['add_schedule'])) {
    $room_id = intval($_POST['room_id']);
    $user_id = intval($_POST['user_id']); // người tạo / giảng viên chịu trách nhiệm
    $subject_id = intval($_POST['subject_id']);
    $start_time = trim($_POST['start_time']); // expecting full datetime "YYYY-MM-DD HH:MM:SS" or "YYYY-MM-DDTHH:MM"
    $end_time = trim($_POST['end_time']);
    $note = trim($_POST['note'] ?? '');

    // normalize if input uses date + time separate: handle in view; here assume correct format
    $res = $sFn->addSchedule($room_id, $user_id, $subject_id, $start_time, $end_time, $note);

    if ($res === true) {
        header("Location: ../views/schedule.php?msg=added");
    } elseif ($res === "conflict") {
        header("Location: ../views/schedule.php?msg=conflict");
    } else {
        header("Location: ../views/schedule.php?msg=error");
    }
    exit;
}

// Update
if (isset($_POST['update_schedule'])) {
    $schedule_id = intval($_POST['schedule_id']);
    $room_id = intval($_POST['room_id']);
    $user_id = intval($_POST['user_id']);
    $subject_id = intval($_POST['subject_id']);
    $start_time = trim($_POST['start_time']);
    $end_time = trim($_POST['end_time']);
    $note = trim($_POST['note'] ?? '');

    $res = $sFn->updateSchedule($schedule_id, $room_id, $user_id, $subject_id, $start_time, $end_time, $note);
    if ($res === true) {
        header("Location: ../views/schedule.php?msg=updated");
    } elseif ($res === "conflict") {
        header("Location: ../views/schedule.php?msg=conflict");
    } else {
        header("Location: ../views/schedule.php?msg=error");
    }
    exit;
}

// Delete
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if ($sFn->deleteSchedule($id)) {
        header("Location: ../views/schedule.php?msg=deleted");
    } else {
        header("Location: ../views/schedule.php?msg=error");
    }
    exit;
}
?>
