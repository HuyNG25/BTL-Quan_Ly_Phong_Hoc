<?php
session_start();
require_once __DIR__ . '/../functions/UserFunctions.php';

// basic check: only admin allowed (adjust as needed)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$userFn = new UserFunctions();

// Add user
if (isset($_POST['add_user'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // Đã sửa để lưu Plain Text trong UserFunctions
    $role = $_POST['role'] ?? 'giangvien';

    if ($userFn->addUser($fullname, $email, $password, $role)) {
        header("Location: ../views/users.php?msg=added");
    } else {
        header("Location: ../views/users.php?msg=error");
    }
    exit;
}

// Update user
if (isset($_POST['update_user'])) {
    $id = intval($_POST['user_id']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $role = $_POST['role'] ?? 'giangvien';

    if ($userFn->updateUser($id, $fullname, $email, $role)) {
        header("Location: ../views/users.php?msg=updated");
    } else {
        header("Location: ../views/users.php?msg=error");
    }
    exit;
}

// Change password
if (isset($_POST['change_password'])) {
    $id = intval($_POST['user_id']);
    $newPass = trim($_POST['new_password']); // Đã sửa để lưu Plain Text trong UserFunctions

    if ($userFn->changePassword($id, $newPass)) {
        header("Location: ../views/users.php?msg=pwchanged");
    } else {
        header("Location: ../views/users.php?msg=error");
    }
    exit;
}

// Delete user
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if ($userFn->deleteUser($id)) {
        header("Location: ../views/users.php?msg=deleted");
    } else {
        header("Location: ../views/users.php?msg=error");
    }
    exit;
}
?>
