<?php
session_start();
require_once __DIR__ . '/../functions/UserFunctions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

$userFn = new UserFunctions();
$user = $userFn->login($email, $password);

if ($user) {
    // store essential info in session (avoid storing password)
    $_SESSION['user'] = [
        'user_id' => $user['user_id'],
        'fullname' => $user['fullname'],
        'email' => $user['email'],
        'role' => $user['role']
    ];

    // redirect by role
    if ($user['role'] === 'admin') {
        header("Location: ../views/dashboard_admin.php");
    } else {
        header("Location: ../views/schedule.php");
    }
    exit;
} else {
    echo "<script>alert('Sai email hoặc mật khẩu'); window.location.href='../login.php';</script>";
    exit;
}
?>
