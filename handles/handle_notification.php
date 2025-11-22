<?php
session_start();
// Đảm bảo đường dẫn này đúng
require_once __DIR__ . '/../functions/NotificationFunctions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$notiFn = new NotificationFunctions();

if (isset($_POST['add_noti'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']); // Tương ứng với cột 'message'
    $admin_id = $_SESSION['user']['user_id']; // Tương ứng với cột 'sender_id'

    if ($notiFn->addNotification($title, $content, $admin_id)) {
        header("Location: ../views/admin/notifications_admin.php?msg=added");
    } else {
        header("Location: ../views/admin/notifications_admin.php?msg=add_error");
    }
    exit;
}

if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']); // Tương ứng với cột 'noti_id'
    if ($notiFn->deleteNotification($id)) {
        header("Location: ../views/admin/notifications_admin.php?msg=deleted");
    } else {
        header("Location: ../views/admin/notifications_admin.php?msg=delete_error");
    }
    exit;
}
?>
