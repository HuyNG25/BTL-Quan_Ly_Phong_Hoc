<?php
session_start();
require_once __DIR__ . '/../functions/UserFunctions.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$userFn = new UserFunctions();
$id = $_SESSION['user']['user_id'];

if (isset($_POST['update_profile'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);

    if ($userFn->updateUser($id, $fullname, $email, $_SESSION['user']['role'])) {
        $_SESSION['user']['fullname'] = $fullname;
        $_SESSION['user']['email'] = $email;
        header("Location: ../views/admin/profile_admin.php?msg=updated");
    } else {
        header("Location: ../views/admin/profile_admin.php?msg=error");
    }
    exit;
}

if (isset($_POST['change_password'])) {
    $newPass = trim($_POST['new_password']);
    if ($userFn->changePassword($id, $newPass)) {
        header("Location: ../views/admin/profile_admin.php?msg=pwchanged");
    } else {
        header("Location: ../views/admin/profile_admin.php?msg=error");
    }
    exit;
}
?>
