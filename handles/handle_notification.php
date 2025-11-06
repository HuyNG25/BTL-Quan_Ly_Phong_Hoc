<?php
session_start();
require_once __DIR__ . '/../functions/NotificationFunctions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$notiFn = new NotificationFunctions();

if (isset($_POST['add_noti'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $admin_id = $_SESSION['user']['user_id'];

    $notiFn->addNotification($title, $content, $admin_id);
    header("Location: ../views/admin/notifications_admin.php?msg=added");
    exit;
}

if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $notiFn->deleteNotification($id);
    header("Location: ../views/admin/notifications_admin.php?msg=deleted");
    exit;
}
?>
